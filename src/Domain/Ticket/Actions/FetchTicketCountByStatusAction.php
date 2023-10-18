<?php

namespace Domain\Ticket\Actions;

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Enums\Status;
use Domain\Ticket\Tappable\FilterForOwnUser;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchTicketCountByStatusAction extends Action
{
    public function execute(): array
    {
        $statuses = Status::getCasesBasedOnRole();

        $counts = [];

        foreach ($statuses as $status) {
            $counts[] = Ticket::query()
                ->tap(new FilterForOwnUser(attribute: 'assignee_id'))
                ->whereStatus($status)
                ->count();
        }

        return $counts;
    }
}