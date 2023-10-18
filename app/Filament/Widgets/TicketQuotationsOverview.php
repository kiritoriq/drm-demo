<?php

namespace App\Filament\Widgets;

use Domain\Shared\Ticket\Models\Quotation;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\Quotation\ResolveTotalAmountWidgetAction;
use Domain\Ticket\Actions\Quotation\ResolveTotalApprovedWidgetAction;
use Domain\Ticket\Actions\Quotation\ResolveTotalQtyWidgetAction;
use Domain\Ticket\Tappable\Quotation\FilterForOwnUser;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class TicketQuotationsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 2;

    public static function canView(): bool
    {
        return ! Role::customersRole();
    }

    protected function getCards(): array
    {
        return [
            ResolveTotalQtyWidgetAction::resolve()
                ->execute(),

            ResolveTotalAmountWidgetAction::resolve()
                ->execute(),

            ResolveTotalApprovedWidgetAction::resolve()
                ->execute()
        ];
    }
}
