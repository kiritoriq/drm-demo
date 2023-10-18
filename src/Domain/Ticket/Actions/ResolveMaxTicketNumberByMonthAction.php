<?php

namespace Domain\Ticket\Actions;

use Domain\Shared\Ticket\Models\Ticket;
use Illuminate\Support\Facades\DB;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveMaxTicketNumberByMonthAction extends Action
{
    public function execute()
    {
        $query = Ticket::query()
            ->select(DB::raw('LPAD(COALESCE(MAX(RIGHT(ticket_number, 4))+1, 1), 4, "0") as max_number'))
            ->whereYear('created_at', now()->format('Y'))
            ->whereMonth('created_at', now()->format('m'))
            ->first();

        return $query->max_number;
    }
}