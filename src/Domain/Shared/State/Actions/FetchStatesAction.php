<?php

namespace Domain\Shared\State\Actions;

use Domain\Shared\State\Models\State;
use Illuminate\Database\Eloquent\Collection;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchStatesAction extends Action
{
    public function execute(): Collection | State
    {
        return State::query()
            ->get();
    }
}