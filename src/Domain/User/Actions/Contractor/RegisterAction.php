<?php

namespace Domain\User\Actions\Contractor;

use App\DataTransferObjects\User\RegisterData;
use App\Exceptions\User\EmailAlreadyTakenException;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Illuminate\Support\Facades\Hash;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class RegisterAction extends Action
{
    public function execute(
        RegisterData $contactInfoData,
        array $services,
        array $locationCoverages
    ): User
    {
        $exists = User::query()
            ->where('email', $contactInfoData->email)
            ->get();

        foreach ($exists as $exist) {
            if ($exist->hasRole(Role::contractor->value)) {
                throw new EmailAlreadyTakenException();
            }
        }

        return value(
            function (User $user) use ($services, $locationCoverages) {
                $user->assignRole(Role::contractor->value);

                $user->offeredServices()->attach($services);

                $user->locationCoverages()->attach($locationCoverages);

                return $user;
            },

            user: User::query()
                ->create([
                    'name' => $contactInfoData->name,
                    'email' => $contactInfoData->email,
                    'password' => Hash::make($contactInfoData->password),
                    'vendor_type' => $contactInfoData->vendorType,
                    'phone' => $contactInfoData->contactNumber,
                    'whatsapp_number' => $contactInfoData->whatsappNumber,
                    'company_name' => $contactInfoData->businessName,
                    'company_description' => $contactInfoData->businessDescription,
                    'office_address' => $contactInfoData->businessAddress,
                    'status' => 1
                ])
        );
    }
}
