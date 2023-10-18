<?php

namespace Domain\User\Actions\Contractor\Wallet\Credited;

use App\Exceptions\Wallet\CreatedException;
use Domain\User\Contracts\Exception\SentNotification;
use Infrastructure\OneSignal\DataTransferObjects\Notification\SentNotificationData;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class SentNotificationAction extends Action implements SentNotification
{
    public function execute(SentNotificationData $data): void
    {
        if ($data->errors) {
            report(new CreatedException(data: $data->errors));
        }
    }
}