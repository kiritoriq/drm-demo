<?php

namespace Domain\Ticket\Actions\Dashboard;

use Illuminate\Contracts\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolvePercentageDifferenceQtyAction extends Action
{
    public function execute(
        Builder $query
    ): array
    {
        $currentQty = $query->newQuery()
            ->whereMonth('created_at', now()->format('m'))
            ->whereYear('created_at', now()->format('Y'))
            ->count();
        
        $lastQty = $query->newQuery()
            ->whereMonth('created_at', now()->subMonth()->format('m'))
            ->whereYear('created_at', now()->format('Y'))
            ->count();

        $totalDif = 0;

        if ($currentQty > 0 && $lastQty <= 0) {
            $totalDif = 100;
        }

        if ($lastQty > 0) {
            $totalDif = (($currentQty - $lastQty) / $lastQty) * 100;
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