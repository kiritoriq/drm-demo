<?php

namespace Infrastructure\OneSignal\Factories;

use Domain\Shared\User\Models\User;
use Infrastructure\Notification\Contracts\Device;
use Infrastructure\Notification\Contracts\Validation\Device as Validation;
use Infrastructure\Notification\Models\Device as Model;
use Infrastructure\OneSignal\Actions\Device\DeleteDeviceAction;
use Infrastructure\OneSignal\Actions\Device\UpdateDeviceAction;
use Infrastructure\OneSignal\DataTransferObjects\Device\DeviceData;
use Infrastructure\OneSignal\DataTransferObjects\Device\UpdateDeviceData;

class DeviceFactory implements Device
{
    public function update(User $user, array $data): Model
    {
        return UpdateDeviceAction::resolve()
            ->execute(
                data: UpdateDeviceData::resolveFrom($data),
                user: $user,
            );
    }

    public function delete(User $user, array $data): void
    {
        DeleteDeviceAction::resolve()
            ->execute(
                data: DeviceData::resolveFrom($data),
                user: $user,
            );
    }
}
