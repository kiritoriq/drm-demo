<?php

namespace Domain\Task\Actions;

use Carbon\Carbon;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Project;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Models\Branch;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Task\JobStatus;
use Illuminate\Support\Collection;
use KoalaFacade\DiamondConsole\Foundation\Action;
use Spatie\Activitylog\Models\Activity;
use stdClass;

readonly class FetchActivityLogAction extends Action
{
    public function execute(
        User $user,
        Ticket $ticket
    ): Collection | Ticket {
        unset($ticket->project_id, $ticket->raised_by_id, $ticket->assignee_id, $ticket->priority, $ticket->due_at);
        $ticket->created_at = $ticket->created_at;
        $ticket->last_updated_at = $ticket->updated_at;
        unset($ticket->updated_at);

        $tasks = Task::query()
            ->select('id', 'task_number', 'title', 'description', 'status')
            ->where('ticket_id', $ticket->id)
            ->where('assignee_id', $user->id)
            ->get();

        $tasks->map(function ($task) use ($ticket) {
            $task_logs = [];
            $logs = Activity::query()
                ->whereMorphedTo(relation: 'subject', model: Task::class)
                ->whereIn('event', ['created', 'updated'])
                ->where('subject_id', $task->id)
                ->whereNotNull('properties->attributes->status')
                ->latest('created_at')
                ->limit(50)
                ->get();

            $task->last_read_at = null;
            $task->has_unread_logs = Activity::query()
                ->whereMorphedTo(relation: 'subject', model: Task::class)
                ->whereIn('event', ['created', 'updated'])
                ->where('subject_id', $task->id)
                ->whereNotNull('properties->attributes->status')
                ->latest('created_at')
                ->whereNull('read_at')
                ->limit(50)
                ->exists();

            if (filled ($logs)) {
                $task->last_read_at = filled ($logs[0]->read_at) ? Carbon::parse($logs[0]->read_at) : null;
            }

            foreach ($logs as $log) {
                $task_logs[] = [
                    'log_id' => $log->id,
                    'event' => $log->event,
                    'description' => $log->description,
                    'created_at' => $log->created_at,
                    'read_at' => filled ($log->read_at) ? Carbon::parse($log->read_at) : null,
                    'previous_status' => ($log->event == 'updated' ? Str::headline(JobStatus::make($log->properties['old']['status'])->name) : ''),
                    'changes_status' => ($log->event == 'updated' ? Str::headline(JobStatus::make($log->properties['attributes']['status'])->name) : Str::headline(JobStatus::New->name))
                ];
            }

            $task->logs = $task_logs;

            return $task;
        });

        $ticket->is_last_log_read = true;
        $unreadLogs = Activity::query()
            ->whereMorphedTo(relation: 'subject', model: Task::class)
            ->whereIn('event', ['created', 'updated'])
            ->whereIn('subject_id', $tasks->pluck('id')->toArray())
            ->whereNotNull('properties->attributes->status')
            ->latest('created_at')
            ->whereNull('read_at')
            ->limit(50)
            ->get();

        if (filled ($unreadLogs)) {
            $ticket->is_last_log_read = false;
        }

        $ticket->customer = new stdClass;
        $ticket->customer = User::query()
            ->select('id', 'name', 'email', 'company_name', 'brand_name', 'phone', 'office_address')
            ->where('id', $ticket->customer_id)
            ->first();
        unset($ticket->customer_id);

        $ticket->branch = new stdClass;
        $ticket->branch = Branch::query()
            ->select('name', 'description', 'phone', 'address', 'latitude', 'longitude')
            ->where('id', $ticket->branch_id)
            ->first();
        unset($ticket->branch_id);

        $ticket->tasks = $tasks;

        $ticket->project = Project::select('id', 'name')
            ->find($ticket->project_id);

        return $ticket;
    }
}
