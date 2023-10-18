<?php

namespace App\DataTransferObjects\Notification;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class CreateNotificationData extends DataTransferObject
{
    public function __construct(
        public readonly string $title,
        public readonly string $content,
        public readonly null | string $image = null,
        public readonly array $payload = [],
    ) {
        //
    }
}