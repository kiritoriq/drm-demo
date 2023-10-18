<?php

namespace Domain\Notification\Actions;

use App\DataTransferObjects\Notification\CreateNotificationData;
use Domain\Shared\Foundation\Action;
use Domain\Shared\Foundation\Models\HasNotification;
use Domain\Task\Contracts\Exception\SentNotification;
use Domain\Notification\Actions\CreateNotificationAction;
use Infrastructure\Notification\Contracts\NotificationCallable;
use Infrastructure\OneSignal\DataTransferObjects\Notification\NotificationData;
use Infrastructure\OneSignal\DataTransferObjects\Notification\SentNotificationData;

abstract class Resolve extends Action implements NotificationCallable
{
    use HasNotification;

    public function resolveNotificationData(): NotificationData
    {
        return value(
            fn (NotificationData $notificationData) => new NotificationData(
                headings: $notificationData->headings,
                contents: $notificationData->contents,
                data: [
                    ...$notificationData->data,
                    ...$this->mergeNotificationData(),
                ]
            ),
            notificationData: NotificationData::resolveFrom($this->notification)
        );
    }

    public function execute(CreateNotificationData $data, SentNotification $afterSending): void
    {
        $this->resolveNotification(
            CreateNotificationAction::resolve()
                ->execute(
                    data: $data,
                    user: $this->resolveModel(),
                    type: $this->resolveType(),
                    unlessType: $this->resolveUnlessType()
                )
        );

        if ($this->skipWhen()) {
            return;
        }

        $afterSending
            ->execute(SentNotificationData::resolveFrom(
                $this->resolveSentNotification()
            ));
    }
}