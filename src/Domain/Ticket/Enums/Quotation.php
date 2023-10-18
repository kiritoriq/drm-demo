<?php

namespace Domain\Ticket\Enums;

use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;

enum Quotation: string
{
    use HasCaseResolver;

    case Quoted = 'quoted';

    case ConfirmJob = 'confirm_job';

    case OverdueQuotation = 'overdue_quotation';

    case Unsuccessful = 'unsuccessful';
}
