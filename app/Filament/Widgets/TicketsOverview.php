<?php

namespace App\Filament\Widgets;

use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\Dashboard\ResolveTotalNewTicketWidgetAction;
use Domain\Ticket\Actions\Dashboard\ResolveTotalQuoteRequestedTicketWidgetAction;
use Domain\Ticket\Actions\Task\ResolveWeeklyTaskWidgetAction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class TicketsOverview extends BaseWidget
{
    public static function canView(): bool
    {
        return ! Role::customersRole();
    }

    protected function getCards(): array
    {
        return [
            ResolveTotalNewTicketWidgetAction::resolve()
                ->execute(),

            ResolveTotalQuoteRequestedTicketWidgetAction::resolve()
                ->execute(),

            ResolveWeeklyTaskWidgetAction::resolve()
                ->execute()
        ];
    }
}
