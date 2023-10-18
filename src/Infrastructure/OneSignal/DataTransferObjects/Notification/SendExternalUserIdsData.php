<?php

namespace Infrastructure\OneSignal\DataTransferObjects\Notification;

use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class SendExternalUserIdsData extends Action
{
    public function __construct(
        public readonly NotificationData $notificationData,
        public readonly ExternalUserIdsData $externalUserIdsData,
    ) {
        //
    }
}