<?php

namespace Domain\Ticket\Actions;

use Carbon\Carbon;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Tappable\FilterForOwnUser;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchMonthlyTotalTicketAction extends Action
{
    public function execute()
    {
        $tickets = Ticket::query()
            ->tap(new FilterForOwnUser(attribute: 'assignee_id'))
            ->whereYear('created_at', now()->format('Y'))
            ->get()
            ->groupBy(fn ($query) => Carbon::parse($query->created_at)->format('m'));

        $ticketMonth = [];

        $data = [];

        foreach ($tickets as $key => $value) {
            $ticketMonth[(int)$key] = count($value);
        }

        for ($i = 1; $i <= 12; $i++) {
            if (!empty($ticketMonth[$i])) {
                $data[] = $ticketMonth[$i];
            } else {
                $data[] = 0;
            }
        }

        return $data;
    }
}