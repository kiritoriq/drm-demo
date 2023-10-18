<?php

namespace App\Filament\Widgets;

use Domain\Ticket\Actions\FetchPersonAssignedToTicketAction;
use Domain\Ticket\Actions\FetchTicketCountByAssigneeAction;
use Filament\Widgets\Widget;

class TicketStatusByAssigneeChart extends Widget
{
    protected static string $view = 'filament.widgets.ticket-status-by-assignee';

    protected function getViewData(): array
    {
        return [
            'series' => FetchTicketCountByAssigneeAction::resolve()->execute(),
            'categories' => array_map(fn ($value) => $value['name'], FetchPersonAssignedToTicketAction::resolve()->execute())
        ];
    }
}
