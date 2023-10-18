<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Exceptions\User\InvalidLogoutFieldException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LogoutRequest;
use Illuminate\Http\JsonResponse;
use Infrastructure\Notification\Contracts\Factory;
use Infrastructure\OneSignal\Enums\Platform;

class LogoutController extends Controller
{
    public function __invoke(LogoutRequest $request): JsonResponse
    {
        if (! $request->has('player_id')) {
            throw new InvalidLogoutFieldException;
        }

        if (filled ($request->player_id)) {
            resolve(name: Factory::class)
                ->createDevice()
                ->delete(
                    user: $request->user(),
                    data: [
                        ...$request->all(),
                        'user' => Platform::contractor,
                    ],
                );
        }

        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User successfully logout.',
        ], 200);
    }
}
