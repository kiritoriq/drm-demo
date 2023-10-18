<?php

namespace App\DataTransferObjects\User;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class ForgotPasswordData extends DataTransferObject
{
    public function __construct(
        public readonly string $email
    ) {
    }
}
