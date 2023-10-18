<?php

namespace Domain\Ticket\Actions\Quotation;

use Domain\Shared\Ticket\Models\Quotation;
use Domain\Ticket\Tappable\Quotation\FilterForOwnUser;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveDifferenceApprovedQtyAction extends Action
{
    public function execute(): array
    {
        $currentQty = Quotation::query()
            ->tap(new FilterForOwnUser(attribute: 'raised_by_id'))
            ->where('is_client_agreed', 1)
            ->whereMonth('created_at', now()->format('m'))
            ->whereYear('created_at', now()->format('Y'))
            ->count();
        
        $lastQty = Quotation::query()
            ->tap(new FilterForOwnUser(attribute: 'raised_by_id'))
            ->where('is_client_agreed', 1)
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