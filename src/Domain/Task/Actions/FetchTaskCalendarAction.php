<?php

namespace Domain\Task\Actions;

use Carbon\Carbon;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchTaskCalendarAction extends Action
{
    public function execute(
        User | null $user
    ): array
    {
        $tasks = Task::query()
            ->whereNull('deleted_at')
            ->whereNotNull('assignee_id')
            ->whereNotIn('status', ['failed', 'rejected', 'task_finished'])
            ->whereYear('date_time', now()->format('Y'))
            ->whereRelation(
                'ticket',
                fn (Builder $ticket) => $ticket
                    ->whereNull('deleted_at')
                    ->whereRelation(
                        'branch',
                        fn (Builder $branch) => $branch->whereNull('deleted_at')
                    )
            )
            ->when(
                $user,
                fn (Builder $query) => $query
                    ->where('assignee_id', $user->id)
            )
            ->whereRelation(
                'assignee',
                fn (Builder $query) => $query->whereNull('deleted_at')
            )
            ->get();

        $calendars = [];

        foreach ($tasks as $task) {
            $calendars[] = [
                'id' => $task->id,
                'title' => $task->assignee?->name . ' on Task Number: ' . $task->task_number,
                'start' => Carbon::parse($task->date_time),
                'contractor_name' => $task->assignee?->name,
                'task_title' => $task->title,
                'task_description' => $task->description,
                'ticket_number' => $task->ticket->ticket_number,
                'ticket_subject' => $task->ticket->subject,
                'ticket_description' => $task->ticket->description,
                'customer_name' => $task->ticket->customer->name,
                'outlet_name' => $task->ticket->branch->name,
                'outlet_location' => $task->ticket->branch->address
            ];
        }

        return $calendars;
    }
}
