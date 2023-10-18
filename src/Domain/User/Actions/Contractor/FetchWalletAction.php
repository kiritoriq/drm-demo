<?php

namespace Domain\User\Actions\Contractor;

use Carbon\Carbon;
use Domain\Shared\User\Models\ContractorWallet;
use Domain\Shared\User\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchWalletAction extends Action
{
    public function execute(
        array $data,
        User $user
    ): Builder {
        return ContractorWallet::query()
            ->where('user_id', $user->id)
            ->when(
                $data && $data['date_from'] && $data['date_to'],
                fn (Builder $query) => $query
                    ->whereBetween(
                        column: 'created_at',
                        values: [
                            Carbon::parse($data['date_from'])->startOfDay()->format('Y-m-d H:i:s'),
                            Carbon::parse($data['date_to'])->endOfDay()->format('Y-m-d H:i:s')
                        ]
                    )
            )
            ->latest('created_at');
    }
}
