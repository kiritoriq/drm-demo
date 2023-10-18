<?php

namespace Infrastructure\OneSignal\Enums;

use Domain\Shared\Foundation\Support\Str;

class Blueprint
{
    public static function addDevice(): string
    {
        return '/api/v1/players';
    }

    public static function editDevice(string $playerId): string
    {
        return Str::of(string: '/api/v1/players/{playerId}')
            ->replace(search: '{playerId}', replace: $playerId);
    }

    public static function pushNotification(): string
    {
        return '/api/v1/notifications';
    }

    public static function deleteDevice(string $playerId): string
    {
        return Str::of(string: '/api/v1/players/{playerId}')
            ->replace(search: '{playerId}', replace: $playerId);
    }
}
