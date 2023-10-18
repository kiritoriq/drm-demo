<?php

namespace Domain\Ticket\Actions\Dashboard;

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Ticket\Tappable\FilterForOwnUser;
use Filament\Widgets\StatsOverviewWidget\Card;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveTotalSolvedTicketWidgetAction extends Action
{
    public function execute(): Card
    {
        $description = ResolveDifferenceNewTicketAction::resolve()
            ->execute();

        return Card::make(
            label: 'Total Job Done',
            value: Ticket::query()
                ->tap(new FilterForOwnUser(attribute: 'customer_id'))
                ->whereSolvedTicket()
                ->whereMonth('created_at', now()->format('m'))
                ->whereYear('created_at', now()->format('Y'))
                ->count()
        )
            ->description(
                description: $description['value'] . '% ' . $description['desc']
            )
            ->descriptionIcon(
                icon: ($description['desc'] === 'increase' ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
            )
            ->color(
                color: ($description['desc'] === 'increase' ? 'success' : 'danger')
            );
    }
}