<?php

namespace App\DataTransferObjects\User;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class LoginData extends DataTransferObject
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string | null $playerId
    ) {
    }
}
