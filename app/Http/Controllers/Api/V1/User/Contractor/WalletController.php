<?php

namespace App\Http\Controllers\Api\V1\User\Contractor;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\GetContractorWalletRequest;
use App\Http\Resources\Wallet\Collection;
use Domain\User\Actions\Contractor\FetchWalletAction;
use Illuminate\Contracts\Support\Responsable;

class WalletController extends Controller
{
    public function index(GetContractorWalletRequest $request): Responsable
    {
        return new Collection(
            resource: FetchWalletAction::resolve()
                ->execute(
                    data: $request->validated(),
                    user: $request->user()
                )
                ->paginate(perPage: $request->per_page ?? 15)
        );
    }
}
