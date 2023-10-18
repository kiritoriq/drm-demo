<?php

namespace App\DataTransferObjects\User\Contractor;

use KoalaFacade\DiamondConsole\Foundation\DataTransferObject;

readonly class ContractorData extends DataTransferObject
{
    public function __construct(
        public readonly string | null $vendorType,
        public readonly string | null $businessName,
        public readonly string | null $businessDescription,
        public readonly string | null $businessEmail,
        public readonly string | null $businessPhone,
        public readonly string | null $businessAddress,
        public readonly string | null $whatsappNumber,
        public readonly string | null $businessLogo
    ) {
    }
}