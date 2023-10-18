<?php

namespace App\DataTransferObjects\User;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class UpdatePasswordData extends DataTransferObject
{
    public function __construct(
        public readonly string $oldPassword,
        public readonly string $password
    ) {
    }
}
