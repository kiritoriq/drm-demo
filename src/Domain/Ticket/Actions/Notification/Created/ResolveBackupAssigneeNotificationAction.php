<?php

namespace Domain\Ticket\Actions\Notification\Created;

use App\Filament\Resources\TicketResource;
use Domain\Shared\Ticket\Models\Ticket;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveBackupAssigneeNotificationAction extends Action
{
    public function execute(Ticket $ticket, array $assignees): void
    {
        $sentId = [];

        foreach ($assignees as $user) {
            if (! in_array ($user->id, $sentId)) {
                $user->notify(
                    Notification::make()
                        ->title(__('notification.web.ticket.created.title'))
                        ->body(__('notification.web.ticket.created.content', [
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