<?php

namespace Domain\Ticket\Enums;

use BackedEnum;
use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;
use UnitEnum;

enum StatusColor: string
{
    use HasCaseResolver;

    case New = '#008ffb';

    case QuoteRequested = '#00e396';

    case Quoted = '#feb019';

    case InProgress = '#ff4560';

    case Solved = '#775dd0';

    case InvoiceDue = '#008ffb';

    case InvoiceOverdue = '#00e396';

    public static function getValues(): array
    {
        $value = [];

        foreach (self::cases() as $case) {
            $value[] = $case->value;
        }

        return $value;
    }

    public static function resolveValue($value)
    {
        $cases = self::cases();
        $matchCase = null;

        foreach ($cases as $case) {
            if ($case->name == $value) {
                $matchCase = $case->value;
            }
        }

        return $matchCase;
    }
}
