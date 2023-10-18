<?php

namespace Infrastructure\OneSignal\Actions\Device;

use Domain\Shared\User\Models\User;
use Infrastructure\OneSignal\DataTransferObjects\Device\DeviceData;
use Infrastructure\OneSignal\DataTransferObjects\Device\UpdateDeviceData;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class DeleteDeviceAction extends Action
{
    public function execute(DeviceData $data, User $user): void
    {
        UpdateDeviceAction::resolve()
            ->execute(
                data: new UpdateDeviceData(
                    externalUserId: '',
                    deviceData: $data
                ),
                user: $user,
            );
    }
}