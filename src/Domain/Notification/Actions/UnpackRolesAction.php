<?php

namespace Domain\Notification\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class UnpackRolesAction extends Action
{
    public function execute(Collection | int $roles): array
    {
        return match (true) {
            $roles instanceof Collection => (array) $roles->pluck('id')->toArray(),
            is_int($roles) => Arr::wrap($roles)
        };
    }
}