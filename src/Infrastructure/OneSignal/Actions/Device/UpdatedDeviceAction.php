<?php

namespace Infrastructure\OneSignal\Actions\Device;

use Domain\Shared\User\Models\User;
use Infrastructure\OneSignal\DataTransferObjects\Device\DeviceData;
use Infrastructure\OneSignal\DataTransferObjects\Device\UpdatedDeviceData;
use Infrastructure\OneSignal\Exceptions\UpdatedDeviceException;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class UpdatedDeviceAction extends Action
{
    public function execute(UpdatedDeviceData $data, DeviceData $deviceData, User $user)
    {
        if (! $data->success) {
            return throw new UpdatedDeviceException();
        }

        return value(
            fn (array $attributes, array $values) => $user
                ->devices()
                ->firstOrCreate(attributes: $attributes, values: $values),

            attributes: [
                'identifier' => $deviceData->playerId,
            ],

            values: []
        );
    }
}