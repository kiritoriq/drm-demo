<?php

namespace Domain\User\Actions;

use App\DataTransferObjects\User\ForgotPasswordData;
use Domain\Shared\User\Models\User;
use Illuminate\Support\Facades\Password;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ForgotPasswordAction extends Action
{
    public function execute(ForgotPasswordData $data): void
    {
        value(
            function (User $user) {
                $token = Password::createToken(user: $user);

                $user->sendResetPasswordLink($token);
            },

            user: User::query()
                ->where('email', $data->email)
                ->first()
        );
    }
}
