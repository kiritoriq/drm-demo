<?php

namespace Domain\User\Actions\Customer;

use App\DataTransferObjects\User\UpsertCustomerData;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Illuminate\Support\Facades\Hash;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class UpsertCustomerAction extends Action
{
    public function execute(UpsertCustomerData $data): User
    {
        return value (
            function (User $user) {
                $user->assignRole(Role::customer->value);

                return $user;
            },

            user: User::query()
                ->create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'password' => Hash::make($data->password),
                    'company_name' => $data->companyName,
                    'brand_name' => $data->brandName,
                    'phone' => $data->phone,
                    'office_address' => $data->officeAddress
                ])
        );
    }
} 