<?php

namespace App\Filament\Widgets;

use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\Dashboard\ResolveTotalNewTicketWidgetAction;
use Domain\Ticket\Actions\Dashboard\ResolveTotalProgressTicketWidgetAction;
use Domain\Ticket\Actions\Dashboard\ResolveTotalQuotedTicketWidgetAction;
use Domain\Ticket\Actions\Dashboard\ResolveTotalQuoteRequestedTicketWidgetAction;
use Domain\Ticket\Actions\Dashboard\ResolveTotalSolvedTicketWidgetAction;
use Domain\Ticket\Actions\Quotation\ResolveTotalApprovedQtyWidgetAction;
use Domain\Ticket\Actions\Quotation\ResolveTotalQtyWidgetAction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Illuminate\Contracts\Auth\Authenticatable;

class CustomerTicketOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 3;

    public static function canView(): bool
    {
        return Role::customersRole();
    }

    protected function getCards(): array
    {
        $stats = [
            ResolveTotalNewTicketWidgetAction::resolve()
                ->execute(),

            ResolveTotalProgressTicketWidgetAction::resolve()
                ->execute(),

            ResolveTotalSolvedTicketWidgetAction::resolve()
                ->execute()
        ];

        if (resolve(Authenticatable::class)->attachedBranches()->exists()) {
            $stats = [
                ...$stats,
                ...[
                    ResolveTotalQuoteRequestedTicketWidgetAction::resolve()
                        ->execute(),

                    ResolveTotalQuotedTicketWidgetAction::resolve()
                        ->execute(),
                ]
            ];
        }

        return $stats;
    }
}
