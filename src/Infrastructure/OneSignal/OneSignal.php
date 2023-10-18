<?php

namespace Infrastructure\OneSignal;

use Illuminate\Contracts\Container\Container;
use Infrastructure\Notification\Contracts\Device;
use Infrastructure\Notification\Contracts\Factory;
use Infrastructure\Notification\Contracts\Validation;
use Infrastructure\OneSignal\Factories\DeviceFactory;
use Infrastructure\OneSignal\Factories\ValidationFactory;

class OneSignal implements Factory
{
    public function __construct(public Container $container)
    {
        //
    }

    public function createDevice(): Device
    {
        return $this->container
            ->make(abstract: DeviceFactory::class);
    }
}
