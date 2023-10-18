<?php

namespace App\Filament\Widgets;

use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Actions\FetchTicketCountByStatusAction;
use Domain\Ticket\Enums\Status;
use Domain\Ticket\Enums\StatusColor;
use Filament\Widgets\Widget;

class TicketByStatusChart extends Widget
{
    protected static string $view = 'filament.widgets.ticket-by-status-chart';

    protected function getViewData(): array
    {
        return [
            'series' => FetchTicketCountByStatusAction::resolve()->execute(),
            'labels' => array_map(fn ($value) => $value->name == 'Solved' ? 'Job Done' : Str::headline($value->name), Status::getCasesBasedOnRole()),
            'colors' => StatusColor::getValues(),
            'ticketCounts' => Ticket::count()
        ];
    }
}
