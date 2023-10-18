<?php

namespace Domain\Ticket\Actions\Dashboard;

use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Tappable\FilterForOwnUser;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Contracts\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveTotalNewTicketWidgetAction extends Action
{
    public function execute(): Card
    {
        $description = ResolveDifferenceNewTicketAction::resolve()
            ->execute();

        return Card::make(
            label: 'Total New Tickets',
            value: Ticket::query()
                ->when(
                    ! Role::hasAny([Role::admin]),
                    fn (Builder $query) => $query->tap(new FilterForOwnUser(attribute: 'assignee_id'))
                )
                ->whereNewTicket()
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