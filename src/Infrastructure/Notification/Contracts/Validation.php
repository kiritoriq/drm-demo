<?php

namespace Infrastructure\Notification\Contracts;

use Infrastructure\Notification\Contracts\Validation\Device;

interface Validation
{
    /**
     * @return Device
     *
     * @throws BindingResolutionException
     */
    public function createDevice(): Device;
}