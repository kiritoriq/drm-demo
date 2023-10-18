<?php

namespace Domain\User\Actions;

use App\DataTransferObjects\User\LoginData;
use App\Exceptions\User\InvalidRoleException;
use App\Exceptions\User\UserInactiveException;
use App\Exceptions\User\UserUnverifiedException;
use App\Exceptions\User\WrongPasswordException;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class LoginAction extends Action
{
    /**
     * @throws WrongPasswordException
     */
    public function execute(LoginData $data): User
    {
        return value(
            function (User $user) use ($data) {
                if (! $user->hasRole('Contractor')) {
                    throw new InvalidRoleException;
                }

                if (! $user->active()) {
                    throw new UserInactiveException();
                }

                if (! Hash::check($data->password, $user->password)) {
                    throw new WrongPasswordException();
                }

                if ($user->isUnverified()) {
                    throw new UserUnverifiedException();
                }

                return $user;
            },

            user: User::query()
                ->where('email', $data->email)
                ->whereRelation(
                    'roles',
                    fn (Builder $query) => $query->where('name', Role::contractor->value)
                )
                ->first()
        );
    }
}
