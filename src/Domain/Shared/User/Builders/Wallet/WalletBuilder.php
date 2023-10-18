<?php

namespace Domain\Shared\User\Builders\Wallet;

use Illuminate\Database\Eloquent\Builder;

class WalletBuilder extends Builder
{
    public function whereRedeemed(): static
    {
        return $this->where('is_redeemed', 1);
    }

    public function whereUnredeemed(): static
    {
        return $this->where('is_redeemed', 0);
    }
}