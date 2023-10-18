<?php

namespace Domain\User\Actions;

use App\DataTransferObjects\User\UpdateProfileData;
use Domain\Shared\User\Models\User;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class UpdateProfileAction extends Action
{
    public function execute(UpdateProfileData $data, User $user): User
    {
        $user->update([
            'email' => $data->email,
            'name' => $data->name,
            'phone' => $data->phone,
            'company_name' => $data->companyName,
        ]);

        return $user->refresh();
    }
}
