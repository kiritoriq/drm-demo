<?php

namespace Domain\Shared\Foundation\Models;

use Domain\Shared\User\Models\ContractorWallet;

trait HasWallet
{
    public readonly ContractorWallet $wallet;

    public function resolveWallet(ContractorWallet $wallet): static
    {
        $this->wallet = $wallet;

        return $this;
    }
}