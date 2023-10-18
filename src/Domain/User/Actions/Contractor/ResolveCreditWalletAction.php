<?php

namespace Domain\User\Actions\Contractor;

use Domain\Shared\User\Models\ContractorWallet;
use Illuminate\Contracts\Auth\Authenticatable;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveCreditWalletAction extends Action
{
    public function execute(ContractorWallet $wallet): void
    {
        $wallet->update([
            'is_redeemed' => 1,
            'redeemed_at' => now(),
            'redeemed_by' => resolve(Authenticatable::class)->id
        ]);

        Wallet\Credited\SendNotificationAction::resolve()
            ->execute($wallet);
    }
}