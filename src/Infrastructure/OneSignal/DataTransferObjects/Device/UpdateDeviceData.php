<?php

namespace Infrastructure\OneSignal\DataTransferObjects\Device;

use Illuminate\Support\Arr;
use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class UpdateDeviceData extends DataTransferObject
{
    public function __construct(
        public null | string $externalUserId,
        public DeviceData $deviceData
    ) {
    }

    public static function resolveFromArray(array $data): static
    {
        return new static(
            externalUserId: Arr::get($data, key: 'external_user_id'),
            deviceData: DeviceData::resolveFrom($data)
        );
    }
}