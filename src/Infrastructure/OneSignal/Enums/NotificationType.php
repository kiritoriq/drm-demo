<?php

namespace Infrastructure\OneSignal\Enums;

use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;

enum NotificationType: int
{
    use HasCaseResolver;

    case Subscribed = 1;

    case Unsubscribed = -2;
}