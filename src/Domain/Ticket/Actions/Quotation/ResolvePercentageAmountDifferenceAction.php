<?php

namespace Domain\Ticket\Actions\Quotation;

use Illuminate\Contracts\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolvePercentageAmountDifferenceAction extends Action
{
    public function execute(
        Builder $query
    ) {
        $currentAmount = $query->newQuery()
            ->whereMonth('created_at', now()->format('m'))
            ->whereYear('created_at', now()->format('Y'))
            ->sum('total_amount');
        
        $lastAmount = $query->newQuery()
            ->whereMonth('created_at', now()->subMonth()->format('m'))
            ->whereYear('created_at', now()->format('Y'))
            ->sum('total_amount');

        $totalDif = 0;

        if ($currentAmount > 0 && $lastAmount <= 0) {
            $totalDif = 100;
        }

        if ($lastAmount > 0) {
            $totalDif = (($currentAmount - $lastAmount) / $lastAmount) * 100;
        }

        $desc = 'decrease';

        if ($totalDif >= 0) {
            $desc = 'increase';
        }

        return [
            'value' => abs(number_format($totalDif, 2, '.')),
            'desc' => $desc
        ];
    }
}