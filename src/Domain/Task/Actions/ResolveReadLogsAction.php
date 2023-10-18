<?php

namespace Domain\Task\Actions;

use Carbon\Carbon;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Task;
use Domain\Ticket\Enums\Task\JobStatus;
use KoalaFacade\DiamondConsole\Foundation\Action;
use Spatie\Activitylog\Models\Activity;

readonly class ResolveReadLogsAction extends Action
{
    public function execute(Task $task)
    {
        Activity::query()
            ->whereMorphedTo(relation: 'subject', model: Task::class)
            ->where('subject_id', $task->id)
            ->update([
                'read_at' => now()->format('Y-m-d H:i:s')
            ]);

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

        if (filled($logs)) {
            $task->last_read_at = filled($logs[0]->read_at) ? Carbon::parse($logs[0]->read_at) : null;
        }

        foreach ($logs as $log) {
            $task_logs[] = [
                'log_id' => $log->id,
                'event' => $log->event,
                'description' => $log->description,
                'created_at' => $log->created_at,
                'read_at' => filled($log->read_at) ? Carbon::parse($log->read_at) : null,
                'previous_status' => ($log->event == 'updated' ? Str::headline(JobStatus::make($log->properties['old']['status'])->name) : ''),
                'changes_status' => ($log->event == 'updated' ? Str::headline(JobStatus::make($log->properties['attributes']['status'])->name) : Str::headline(JobStatus::New->name))
            ];
        }

        $task->logs = $task_logs;

        return $task;
    }
}
