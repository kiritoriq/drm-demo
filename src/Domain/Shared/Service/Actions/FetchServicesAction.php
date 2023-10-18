<?php

namespace Domain\Shared\Service\Actions;

use Domain\Shared\Service\Models\Service;
use Illuminate\Database\Eloquent\Collection;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchServicesAction extends Action
{
    public function execute(): Collection | Service
    {
        return Service::query()
            ->get();
    }
}