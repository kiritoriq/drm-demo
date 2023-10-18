<?php

namespace Domain\User\Contracts\Exception;

use Infrastructure\OneSignal\DataTransferObjects\Notification\SentNotificationData;

interface SentNotification
{
    public function execute(SentNotificationData $data): void;
}
