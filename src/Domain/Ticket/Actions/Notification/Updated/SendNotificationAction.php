<?php

namespace Domain\Ticket\Actions\Notification\Updated;

use App\Filament\Resources\TicketResource;
use Domain\Shared\Foundation\Support\Str;
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
                $ticket->assignee
            ],
            ...[
                $ticket->customer,
            ],
            ...[
                $ticket->raisedBy
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
                        ->title(__('notification.web.ticket.status_updated.title'))
                        ->body(__('notification.web.ticket.status_updated.content', [
                            'ticket_number' => $ticket->ticket_number,
                            'status' => Str::headline($ticket->status->name),
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