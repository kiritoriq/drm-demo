<?php

namespace Infrastructure\Notification\Contracts;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Validation\ValidationException;

interface Factory
{
    /**
     * @return Device
     *
     * @throws ValidationException | BindingResolutionException
     */
    public function createDevice(): Device;
}
