<?php

namespace Domain\Shared\Foundation\Enums;

use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;

enum Mode: string
{
    use HasCaseResolver;

    case Live = 'live';

    case Sandbox = 'sandbox';
}
