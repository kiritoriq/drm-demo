<?php

namespace Domain\Ticket\Enums\Task;

use Domain\Shared\Foundation\Concerns\Enum\HasCaseResolver;

enum JobStatus: string
{
    use HasCaseResolver;

    case New = 'new';

    case PendingSiteVisit = 'pending_site_visit';

    case PendingQuotation = 'pending_quotation';

    case JobAwarded = 'job_awarded';

    case Failed = 'failed';

    case Progress = 'progress';

    case ProgressCompleted = 'progress_completed';
    
    case ProgressQc = 'progress_qc';

    case TaskFinished = 'task_finished';

    case QcRejected = 'qc_rejected';

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
}
