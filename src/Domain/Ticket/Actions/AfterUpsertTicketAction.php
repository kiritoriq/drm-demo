<?php

namespace Domain\Ticket\Actions;

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Actions\Asset\ResolveTicketAssetAction;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class AfterUpsertTicketAction extends Action
{
    public function execute(Ticket $ticket, array $data): void
    {
        ResolveTicketAssetAction::resolve()->execute($ticket, $data);
    }
}
