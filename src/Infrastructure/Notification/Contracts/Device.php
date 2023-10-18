<?php

namespace Infrastructure\Notification\Contracts;

use Domain\Shared\User\Models\User;
use Illuminate\Validation\ValidationException;
use Infrastructure\Notification\Models\Device as Model;
use Infrastructure\OneSignal\Exceptions\UpdatedDeviceException;

interface Device
{
    /**
     * @param  User  $user
     * @param  array  $data
     * @return Model
     *
     * @throws UpdatedDeviceException|ValidationException
     */
    public function update(User $user, array $data): Model;

    /**
     * @param  User  $user
     * @param  array  $data
     * @return void
     *
     * @throws UpdatedDeviceException
     * @throws UpdatedDeviceException|ValidationException
     */
    public function delete(User $user, array $data): void;
}
