<?php

namespace Domain\Ticket\Enums;

use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;
use Domain\Shared\User\Enums\Role;

enum Status: string
{
    use HasCaseResolver;

    case New = 'new';

    case QuoteRequested = 'quote_requested';

    case Quoted = 'quoted';

    case InProgress = 'in_progress';

    case Solved = 'solved';

    case InvoiceDue = 'invoice_due';

    case InvoiceOverdue = 'invoice_overdue';

    case Cancelled = 'cancelled';

    public static function make(string $value)
    {
        $cases = self::cases();
        $matchCase = null;

        foreach ($cases as $case) {
            if ($case->value == $value) {
                $matchCase = $case;
            }
        }

        return $matchCase;
    }

    public static function getCasesBasedOnRole(): array
    {
        $cases = [];

        foreach (self::cases() as $case) {
            if (Role::exactlyCustomerRole()) {
                if (! in_array ($case->name, ['InvoiceDue', 'InvoiceOverdue'])) {
                    $cases[] = $case;
                }
            } else {
                $cases[] = $case;
            }
        }

        return $cases;
    }
}
