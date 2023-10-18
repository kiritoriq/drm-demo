<?php

namespace Domain\Task\Actions\Notification\AssigneeUpdated;

use App\Filament\Resources\TicketResource;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Models\User;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class SendNotificationAction extends Action
{
    public function execute(Ticket $ticket, Task $task): void
    {
        $adminUsers = User::query()
            ->whereRelation(
                'roles',
                fn (Builder $query) => $query->where('name', '=', 'Admin')
            )
            ->get();

        $branchUsers = $ticket->branch->users;

        $recipients = [
            ...$adminUsers,
            ...[
                $ticket->customer,
            ],
            ...[
                $ticket->raisedBy
            ],
            ...$branchUsers
        ];

        if (filled ($ticket->assignee)) {
            $recipients = [
                ...$recipients,
                ...[
                    $ticket->assignee
                ]
            ];
        }

        if (filled ($ticket->backupAssignees)) {
            $recipients = [
                ...$recipients,
                ...$ticket->backupAssignees
            ];
        }

        $sentId = [];

        foreach ($recipients as $user) {
            if (! in_array ($user->id, $sentId)) {
                $user->notify(
                    Notification::make()
                        ->title(__('notification.web.task.assignee_updated.title'))
                        ->body(__('notification.web.task.assignee_updated.content', [
                            'task_number' => $task->task_number,
                            'contractor' => $task->assignee->name,
                            'date_time' => now()->format('Y-m-d H:i:s')
                        ]))
                        ->success()
                        ->actions([
                            NotificationAction::make('view')
                                ->url(TicketResource::getUrl('edit', $ticket))
                        ])
                        ->toDatabase()
                );

                array_push($sentId, $user->id);
            }
        }
    }
}