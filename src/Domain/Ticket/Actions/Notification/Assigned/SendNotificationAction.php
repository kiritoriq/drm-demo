<?php

namespace Domain\Ticket\Actions\Notification\Assigned;

use App\Filament\Resources\TicketResource;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Models\User;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class SendNotificationAction extends Action
{
    public function execute(Ticket $ticket): void
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
            ...[
                $ticket->assignee
            ],
            ...$branchUsers
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
                        ->title(__('notification.web.ticket.assigned.title'))
                        ->body(__('notification.web.ticket.assigned.content', [
                            'ticket_number' => $ticket->ticket_number,
                            'user_name' => $ticket->assignee->name,
                            'project' => $ticket->project?->name,
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