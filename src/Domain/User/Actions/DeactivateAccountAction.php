<?php

namespace Domain\User\Actions;

use App\Exceptions\User\UserInactiveException;
use Domain\Shared\User\Models\User;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class DeactivateAccountAction extends Action
{
    public function execute(User $user): bool
    {
        if (! $user->active()) {
            throw new UserInactiveException();
        }

        $user->update([
            'status' => 0
        ]);

        return $user->currentAccessToken()->delete();
    }
}