<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Service\Collection;
use Domain\Shared\Service\Actions\FetchServicesAction;

class GetServiceController extends Controller
{
    public function __invoke()
    {
        return new Collection(
            resource: FetchServicesAction::resolve()
                ->execute()
        );
    }
}