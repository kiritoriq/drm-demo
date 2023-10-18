<?php

namespace Infrastructure\Notification;

use Illuminate\Support\ServiceProvider;
use Infrastructure\Notification\Contracts\Factory as PushNotification;
use Infrastructure\OneSignal\OneSignal;

class NotificationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PushNotification::class, OneSignal::class);
    }
}
