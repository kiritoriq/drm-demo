<?php

namespace Domain\Task\Actions\Accept;

use App\DataTransferObjects\Task\TaskNumberData;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Domain\Task\Actions\UpdateStatusAction;
use Domain\Ticket\Enums\Task\JobStatus;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveStatusAction extends Action
{
    public function execute(TaskNumberData $data, User $user): Task
    {
        $task = UpdateStatusAction::resolve()
            ->queryUsing(fn () => Task::query())
            ->invalidStatusUsing(fn (Task $task) => ! $task->eligibleToAccept())
            ->abilitiesUsing(fn () => 'accept')
            ->statusUsing(fn () => JobStatus::PendingSiteVisit->value)
            ->extraFieldsUsing(fn () => ['accepted_at' => now()])
            ->afterUsing(function (Task $task) use ($user) {
                $task->processStatuses()->create([
                    'title' => 'Job Accepted',
                    'description' => 'Job accepted by ' . $user->name,
                    'status' => $task->status->value
                ]);

                SendNotificationAction::resolve()
                    ->execute($task);
            })
            ->execute($data, $user);

        return $task;
    }
}
