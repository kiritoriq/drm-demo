<?php

namespace Domain\Ticket\Actions;

use Domain\Shared\Ticket\Models\Ticket;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveTicketTableColorAction extends Action
{
    public function execute(
        Ticket $ticket
    ): string {
        return ResolveTicketReminderAction::resolve()
            ->execute($ticket);
    }
}