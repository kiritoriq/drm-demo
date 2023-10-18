<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\State\Collection;
use Domain\Shared\State\Actions\FetchStatesAction;

class GetStateController extends Controller
{
    public function __invoke()
    {
        return new Collection(
            resource: FetchStatesAction::resolve()
                ->execute()
        );
    }
}