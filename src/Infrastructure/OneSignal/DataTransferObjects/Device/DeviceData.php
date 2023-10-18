<?php

namespace Infrastructure\OneSignal\DataTransferObjects\Device;

use Infrastructure\OneSignal\Enums\Platform;
use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class DeviceData extends DataTransferObject
{
    public function __construct(
        public readonly Platform $user,
        public readonly string $playerId,
    ) {
    }

    public static function resolveFromArray(array $data): static
    {
        $user = $data['user'];

        return new static(
            user: $user instanceof Platform ? $user : Platform::tryFrom($user),
            playerId: $data['player_id'],
        );
    }
}