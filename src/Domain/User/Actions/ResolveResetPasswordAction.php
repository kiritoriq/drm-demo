<?php

namespace Domain\User\Actions;

use Domain\Shared\User\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveResetPasswordAction extends Action
{
    public function execute(array $data)
    {
        return Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );
    }
}
