<?php

namespace Domain\Ticket\Actions;

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Actions\Notification\Reminder\SendNotificationAction;
use KoalaFacade\DiamondConsole\Foundation\Action;
use Spatie\Valuestore\Valuestore;

readonly class ResolveTicketReminderAction extends Action
{
    public function execute(Ticket $ticket)
    {
        $settings = Valuestore::make(config('filament-settings.path'));

        $reminder = $settings->get($ticket->status->value . '_reminder');

        $dateDiff = now()->diffInWeekdays($ticket->updated_at);

        if (
            filled ($reminder) &&
            $dateDiff >= $reminder &&
            filled ($ticket->assignee) &&
            ! $ticket->solvedTicket()
        ) {
            SendNotificationAction::resolve()
                ->execute($ticket);

            return match($ticket->status->value) {
                'new' => '#ff7f27',
                'quote_requested' => '#9cdb0445',
                'quoted' => '#f3700045',
                'in_progress' => '#ff230054',
                'invoice_due' => '#fff200'
            };
        }

        return '';
    }
}