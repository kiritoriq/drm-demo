<?php

namespace Domain\Task\Actions\Complete;

use App\DataTransferObjects\Notification\CreateNotificationData;
use Domain\Shared\Ticket\Models\Task;
use Domain\Task\Actions\Notifiable\ResolveExternalUserIdsAction;
use Domain\Task\Actions\SentNotificationAction;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class SendNotificationAction extends Action
{
    public function execute(Task $task): void
    {
        if ($task->doesntHaveContractor()) {
            return;
        }

        ResolveExternalUserIdsAction::resolve()
            ->resolveTask($task)
            ->execute(
                data: new CreateNotificationData(
                    title: __(key: 'notification.contractor.task.progress_completed.title'),
                    content: __(
                        key: 'notification.contractor.task.progress_completed.content',
                        replace:[
                            'task_number' => $task->task_number,
                        ])
                ),
                afterSending: SentNotificationAction::resolve()
            );
    }
}