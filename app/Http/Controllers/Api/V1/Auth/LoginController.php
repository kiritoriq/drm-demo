<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\DataTransferObjects\User\LoginData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\Auth\Contractor\Resource;
use Domain\User\Actions\LoginAction;
use Illuminate\Contracts\Support\Responsable;
use Infrastructure\Notification\Contracts\Factory;
use Infrastructure\OneSignal\Enums\Platform;

class LoginController extends Controller
{
    public function __invoke(LoginRequest $request): Responsable
    {
        $data = LoginData::resolve($request->validated());

        $user = LoginAction::resolve()
            ->execute(
                data: $data
            );

        if (filled ($request->player_id) && filled ($user)) {
            resolve(name: Factory::class)
                ->createDevice()
                ->update(
                    user: $user,
                    data: [
                        ...$request->all(),
                        'user' => Platform::contractor,
                    ],
                );
        }

        return new Resource(
            resource: $user
        );
    }
}
