<?php

namespace Domain\Ticket\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchBranchHasTicketAction extends Action
{
    public function execute(): array
    {
        return resolve(Authenticatable::class)
            ->branches
            ->toArray();
    }
}