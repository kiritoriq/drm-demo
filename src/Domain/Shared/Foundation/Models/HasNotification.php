<?php

namespace Domain\Shared\Foundation\Models;

use Infrastructure\Notification\Models\Notification;

trait HasNotification
{
    public readonly Notification $notification;

    public function resolveNotification(Notification $notification): static
    {
        $this->notification = $notification;

        return $this;
    }
}
