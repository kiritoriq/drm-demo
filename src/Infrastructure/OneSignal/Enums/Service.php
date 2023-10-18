<?php

namespace Infrastructure\OneSignal\Enums;

use Domain\Shared\Foundation\Enums\Mode;
use Domain\Shared\Foundation\Support\Str;

enum Service: string
{
    case Endpoint = 'endpoint';

    case AppKey = 'app_key';

    case AppId = 'app_id';

    case Mode = 'mode';

    public static function resolve(Service $service): null | string
    {
        return config(
            key: (string) Str::of(string: 'services.onesignal.{key}')
                ->replace(search: '{key}', replace: $service->value)
        );
    }

    public static function resolveUser(Service $service, $platform): null | string
    {
        return config(
            key: (string) Str::of(string: 'services.onesignal.{mode}.{user}.{key}')
                ->replace(search: '{key}', replace: $service->value)
                ->replace(search: '{user}', replace: $platform->value)
                ->replace(search: '{mode}', replace: Mode::tryFrom(value: self::resolve(service: Service::Mode))->value)
        );
    }
}