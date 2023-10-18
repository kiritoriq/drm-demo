<?php

namespace Infrastructure\OneSignal\DataTransferObjects\Notification;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class SentNotificationData extends DataTransferObject
{
    public function __construct(
        public readonly string $id,
        public readonly bool $recipients,
        public readonly array $errors,
    ) {
    }

    public static function resolveFrom(mixed $abstract): static
    {
        if ($abstract instanceof Response) {
            return static::resolveFromResponse($abstract);
        }

        throw new \RuntimeException;
    }

    public static function resolveFromResponse(Response $response): static
    {
        return static::resolve($response->json());
    }

    public static function resolve(array $data): static
    {
        return new static(
            id: Arr::get($data, key: 'id', default: ''),
            recipients: Arr::get($data, key: 'recipients', default: false),
            errors: Arr::get($data, key: 'errors', default: []),
        );
    }
}