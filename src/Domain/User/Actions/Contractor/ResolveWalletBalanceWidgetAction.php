<?php

namespace Domain\User\Actions\Contractor;

use Domain\Shared\User\Models\User;
use KoalaFacade\DiamondConsole\Foundation\Action;
use Filament\Widgets\StatsOverviewWidget\Card;

readonly class ResolveWalletBalanceWidgetAction extends Action
{
    public function execute(User $user): Card
    {
        $totalAmount = $user->transactionHistories()
            ->whereNull('redeemed_at')
            ->sum('amount');

        return Card::make(
                label: 'Current Wallet Balance',
                value: 'RM ' . number_format($totalAmount, 2, '.', ',')
            )
            ->color(color: 'success');
    }
}