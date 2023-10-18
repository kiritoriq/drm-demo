<?php

namespace Infrastructure\OneSignal\Enums;

use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;

enum Platform: string
{
    use HasCaseResolver;

    case contractor = 'contractor';
}
