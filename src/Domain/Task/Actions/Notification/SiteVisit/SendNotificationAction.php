<?php

namespace Domain\Task\Actions\Notification\SiteVisit;

use App\Filament\Resources\TicketResource;
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

        $recipients = [
            ...$adminUsers,
            ...[
                $ticket->assignee
            ]
        ];

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
                        ->title(__('notification.web.task.site_visit_feedback.title', [
                            'task_number' => $task->task_number
                        ]))
                        ->body(__('notification.web.task.site_visit_feedback.content', [
                            'contractor' => $task->assignee->name,
                            'ticket_number' => $ticket->ticket_number,
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