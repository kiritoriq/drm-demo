<?php

namespace Domain\Ticket\Actions\Quotation;

use Domain\Shared\Ticket\Models\Quotation;
use Domain\Ticket\Tappable\Quotation\FilterForOwnUser;
use Filament\Widgets\StatsOverviewWidget\Card;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveTotalAmountWidgetAction extends Action
{
    public function execute(): Card
    {
        $description = ResolveDifferenceTotalAmountAction::resolve()
            ->execute();
        
        return Card::make(
            label: 'Quoted (Total Amount)',
            value: 'RM ' . number_format(Quotation::query()
                ->tap(new FilterForOwnUser(attribute: 'raised_by_id'))
                ->whereMonth('created_at', now()->format('m'))
                ->whereYear('created_at', now()->format('Y'))
                ->sum('total_amount'), 2, '.')
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