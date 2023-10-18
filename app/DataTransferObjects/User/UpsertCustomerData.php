<?php

namespace App\DataTransferObjects\User;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class UpsertCustomerData extends DataTransferObject
{
    public function __construct(
        public readonly string $name,
        public readonly string | null $email,
        public readonly string $password,
        public readonly string | null $companyName,
        public readonly string | null $brandName,
        public readonly string $phone,
        public readonly string | null $officeAddress
    ) {
    }
}