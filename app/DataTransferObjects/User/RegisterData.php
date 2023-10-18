<?php

namespace App\DataTransferObjects\User;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class RegisterData extends DataTransferObject
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
        public readonly string $vendorType,
        public readonly string | null $contactNumber,
        public readonly string | null $whatsappNumber,
        public readonly string | null $businessName,
        public readonly string | null $businessDescription,
        public readonly string | null $businessAddress
    ) {
    }
}
