<?php

namespace Domain\Ticket\Enums;

use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;

enum Priority: string
{
    use HasCaseResolver;

    case Low = 'low';

    case Medium = 'medium';

    case High = 'high';

    case Critical = 'critical';
}
