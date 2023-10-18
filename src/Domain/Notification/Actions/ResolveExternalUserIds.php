<?php

namespace Domain\Notification\Actions;

use Domain\Notification\Actions\Resolve;
use Illuminate\Http\Client\Response;
use Infrastructure\OneSignal\Actions\Notification\SendExternalUserIdsAction;
use Infrastructure\OneSignal\DataTransferObjects\Notification\ExternalUserIdsData;
use Infrastructure\OneSignal\DataTransferObjects\Notification\SendExternalUserIdsData;
use Infrastructure\OneSignal\Enums\Channel;

abstract class ResolveExternalUserIds extends Resolve
{
    abstract public function resolveIds(): array;

    public function resolveSentNotification(): Response
    {
        return SendExternalUserIdsAction::resolve()
            ->execute(
                data: new SendExternalUserIdsData(
                    externalUserIdsData: new ExternalUserIdsData(
                        channel: Channel::Push,
                        ids: $this->resolveIds(),
                    ),
                    notificationData: $this->resolveNotificationData()
                ),
                platform: $this->resolvePlatform()
            );
    }
}