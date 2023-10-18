<?php

namespace Domain\Task\Actions\Failed;

use App\DataTransferObjects\Task\RejectData;
use App\DataTransferObjects\Task\TaskNumberData;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Domain\Task\Actions\UpdateStatusAction;
use Domain\Ticket\Enums\Task\JobStatus;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveStatusAction extends Action
{
    public function execute(TaskNumberData $taskNumberData, RejectData $data, User $user): Task
    {
        $task = UpdateStatusAction::resolve()
            ->queryUsing(fn () => Task::query())
            ->invalidStatusUsing(fn (Task $task) => ! $task->eligibleToReject())
            ->abilitiesUsing(fn () => 'reject')
            ->statusUsing(fn () => JobStatus::Failed->value)
            ->afterUsing(function (Task $task) use ($data, $user) {
                $task
                    ->cancelledTask()
                    ->create([
                        'user_id' => $user->id,
                        'reject_reason' => $data->rejectReason,
                        'description' => $data->description
                    ]);

                $task
                    ->processStatuses()
                    ->create([
                        'title' => 'Job Rejected',
                        'description' => 'Job rejected by ' . $user->name,
                        'status' => $task->status->value
                    ]);

                SendNotificationAction::resolve()
                    ->execute($task);
            })
            ->execute($taskNumberData, $user);

        return $task
            ->load('cancelledTask');
    }
}
