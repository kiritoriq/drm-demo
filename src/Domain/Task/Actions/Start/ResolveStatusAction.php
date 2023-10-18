<?php

namespace Domain\Task\Actions\Start;

use App\DataTransferObjects\Task\TaskNumberData;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Domain\Task\Actions\Notification;
use Domain\Task\Actions\UpdateStatusAction;
use Domain\Ticket\Enums\Task\JobStatus;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveStatusAction extends Action
{
    public function execute(TaskNumberData $data, User $user): Task
    {
        $task = UpdateStatusAction::resolve()
            ->queryUsing(fn () => Task::query())
            ->invalidStatusUsing(fn (Task $task) => ! $task->eligibleToStart())
            ->abilitiesUsing(fn () => 'start')
            ->statusUsing(fn () => JobStatus::Progress->value)
            ->extraFieldsUsing(fn () => ['started_at' => now()])
            ->afterUsing(function (Task $task) {
                $task->processStatuses()->create([
                    'title' => 'Start Work on Client Site',
                    'description' => 'Start work on client site',
                    'status' => $task->status->value
                ]);

                SendNotificationAction::resolve()
                    ->execute($task);
                
                Notification\StatusUpdated\SendNotificationAction::resolve()
                    ->execute(
                        ticket: $task->ticket,
                        task: $task
                    );
            })
            ->execute($data, $user);

        return $task;
    }
}
