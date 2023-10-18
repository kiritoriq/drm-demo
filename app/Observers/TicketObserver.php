<?php

namespace App\Observers;

use App\Filament\Resources\TicketResource;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Actions;
use Domain\Ticket\Actions\Report\GenerateReportDocumentAction;
use Domain\Ticket\Enums\Status;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        Actions\Notification\Created\SendNotificationAction::resolve()
            ->execute($ticket);
    }

    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        if ($ticket->isDirty('status')) {
            Actions\Notification\Updated\SendNotificationAction::resolve()
                ->execute($ticket);
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Listen to the User created event.
     *
     * @param Domain\Shared\Ticket\Models\Ticket
     * @return void
     */
    public function updating(Ticket $ticket)
    {
        if ($ticket->isDirty('assignee_id') && filled ($ticket->assignee_id)) {
            Actions\Notification\Assigned\SendNotificationAction::resolve()
                ->execute($ticket);
        }

        if ($ticket->isDirty('assignee_id') && blank ($ticket->assignee_id)) {
            $unassigned = User::find($ticket->getOriginal('assignee_id'));

            Actions\Notification\Unassigned\SendNotificationAction::resolve()
                ->execute(
                    ticket: $ticket,
                    unassigned: $unassigned
                );
        }

        if ($ticket->isDirty('status')) {
            if ($ticket->status === Status::Solved) {
                GenerateReportDocumentAction::resolve()
                    ->execute($ticket);
            }
        }
    }
}
