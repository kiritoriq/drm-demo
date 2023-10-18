<?php

namespace Infrastructure\Notification\Contracts;

use App\DataTransferObjects\Notification\CreateNotificationData;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Domain\Task\Contracts\Exception\SentNotification;
use Illuminate\Http\Client\Response;
use Infrastructure\OneSignal\DataTransferObjects\Notification\NotificationData;
use Infrastructure\OneSignal\Enums\Platform;

interface NotificationCallable
{
    public function skipWhen(): bool;

    public function mergeNotificationData(): array;

    public function resolveType(): Role;

    public function resolveUnlessType(): Role | null;

    public function resolvePlatform(): Platform;

    public function resolveModel(): User | null;

    public function resolveSentNotification(): Response;

    public function resolveNotificationData(): NotificationData;

    public function execute(CreateNotificationData $data, SentNotification $afterSending);
}
