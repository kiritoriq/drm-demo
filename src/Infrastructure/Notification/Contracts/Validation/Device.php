<?php

namespace Infrastructure\Notification\Contracts\Validation;

use Illuminate\Validation\ValidationException;

interface Device
{
    /**
     * @throws ValidationException
     */
    public function update(array $data): void;

    /**
     * @throws ValidationException
     */
    public function delete(array $data): void;
}
