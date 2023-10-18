<?php

namespace Infrastructure\OneSignal\DataTransferObjects\Notification;

use Infrastructure\OneSignal\Enums\Channel;
use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class ExternalUserIdsData extends DataTransferObject
{
    public function __construct(
        public readonly Channel $channel,
        public readonly array $ids
    ) {
    }
}