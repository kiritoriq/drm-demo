<?php

namespace Domain\Ticket\Actions\Notification\Reminder;

use App\Filament\Resources\TicketResource;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Ticket;
use Filament\Notifications\Notification;
use KoalaFacade\DiamondConsole\Foundation\Action;
use Filament\Notifications\Actions\Action as NotificationAction;

readonly class SendNotificationAction extends Action
{
    public function execute(Ticket $ticket): void
    {
        $recipients = [
            ...[
                $ticket->assignee
            ]
        ];

        $notifications = $ticket->assignee->notifications;

        $sentStatus = true;

        foreach ($notifications as $notification) {
            if (
                strpos($notification->data['body'], $ticket->ticket_number) !== false &&
                strpos($notification->data['title'], 'Reminder') !== false
            ) {
                $sentStatus = false;
            }
        }
        
        if ($sentStatus) {
            $sentId = [];

            foreach ($recipients as $user) {
                if (! in_array ($user->id, $sentId)) {
                    $user->notify(
                        Notification::make()
                            ->title(__(':ticket_status Ticket Reminder', [
                                'ticket_status' => Str::headline($ticket->status->value)
                            ]))
                            ->body(__(':ticket_number is waiting for your action', [
                                'ticket_number' => $ticket->ticket_number
                            ]))
                            ->warning()
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
}