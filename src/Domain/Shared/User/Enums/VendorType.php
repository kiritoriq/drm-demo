<?php

namespace Domain\Shared\User\Enums;

use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;

enum VendorType: string
{
    use HasCaseResolver;

    case Individual = 'individual';

    case Company = 'company';
}