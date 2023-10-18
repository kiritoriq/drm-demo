<?php

namespace Domain\Ticket\Actions\Dashboard;

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Tappable\FilterForOwnUser;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveDifferenceQuotedTicketAction extends Action
{
    public function execute(): array
    {
        return ResolvePercentageDifferenceQtyAction::resolve()
            ->execute(
                query: Ticket::query()
                    ->tap(new FilterForOwnUser(attribute: 'customer_id'))
                    ->whereQuotedTicket()
            );
    }
}