<?php

namespace Infrastructure\OneSignal\DataTransferObjects\Notification;

use Illuminate\Database\Eloquent\Model;
use Infrastructure\Notification\Models\Notification;
use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class NotificationData extends DataTransferObject
{
    public function __construct(
        public readonly array $headings,
        public readonly array $contents,
        public readonly array $data = []
    ) {
    }

    public static function resolveFrom(mixed $abstract): static
    {
        if ($abstract instanceof Notification) {
            return static::resolveFromModel($abstract);
        }

        return throw new \RuntimeException;
    }

    public static function resolveFromModel(Model $notification): static
    {
        return new static(
            headings: [
                'en' => $notification->title,
            ],
            contents: [
                'en' => $notification->content,
            ],
            data: [
                'notification' => $notification->only(attributes: 'id'),
            ]
        );
    }
}