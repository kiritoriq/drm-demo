<?php

namespace Domain\User\Actions;

use App\DataTransferObjects\User\UpdatePasswordData;
use App\Exceptions\User\ExactlySamePasswordException;
use App\Exceptions\User\UnmatchPasswordException;
use Domain\Shared\User\Models\User;
use Illuminate\Support\Facades\Hash;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class UpdatePasswordAction extends Action
{
    public function execute(UpdatePasswordData $data, User $user): User
    {
        if (! Hash::check($data->oldPassword, $user->password)) {
            throw new UnmatchPasswordException();
        }

        if (Hash::check($data->password, $user->password) || $data->password == $data->oldPassword) {
            throw new ExactlySamePasswordException();
        }

        $user->update([
            'password' => Hash::make($data->password),
        ]);

        return $user->refresh();
    }
}
