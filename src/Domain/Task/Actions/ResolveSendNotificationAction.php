<?php

namespace Domain\Task\Actions;

use Domain\Shared\Ticket\Models\Task;
use Domain\Task\Actions\Finished\SendNotificationAction as FinishedNotification;
use Domain\Task\Actions\JobAwarded\SendNotificationAction as AwardedNotification;
use Domain\Task\Actions\QcProgress\SendNotificationAction as QcNotification;
use Domain\Task\Actions\Rejected\SendNotificationAction as RejectedNotification;
use Domain\Ticket\Enums\Task\JobStatus;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveSendNotificationAction extends Action
{
    public function execute(
        JobStatus $status,
        Task $task
    ): void {
        if ($status === JobStatus::JobAwarded) {
            AwardedNotification::resolve()->execute($task);
        }

        if ($status === JobStatus::ProgressQc) {
            QcNotification::resolve()->execute($task);
        }

        if ($status === JobStatus::QcRejected) {
            RejectedNotification::resolve()->execute($task);
        }

        if ($status === JobStatus::TaskFinished) {
            FinishedNotification::resolve()->execute($task);
        }
    }
}