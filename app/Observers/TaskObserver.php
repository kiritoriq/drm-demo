<?php

namespace App\Observers;

use Domain\Branch\Actions\Asset\ResolveAssetPreventiveServiceAction;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\ContractorWallet;
use Domain\Task\Actions\New\SendNotificationAction;
use Domain\Task\Actions\Notification;
use Domain\Task\Actions\ResolveSendNotificationAction;
use Domain\Ticket\Enums\Task\JobStatus;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        Notification\Created\SendNotificationAction::resolve()
            ->execute(
                ticket: $task->ticket,
                task: $task
            );
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }

    /**
     * Listen to the User created event.
     *
     * @param Domain\Shared\Ticket\Models\Task
     * @return void
     */
    public function updating(Task $task)
    {
        if ($task->isDirty('assignee_id')) {
            SendNotificationAction::resolve()
                ->execute($task);

            Notification\AssigneeUpdated\SendNotificationAction::resolve()
                ->execute($task->ticket, $task);
        }

        if ($task->isDirty('status')) {
            $statusName = Str::headline($task->status->name);
            $task->processStatuses()->create([
                'title' => $statusName,
                'description' => 'Job status changed to ' . $statusName,
                'status' => $task->status->value
            ]);

            if ($task->status === JobStatus::TaskFinished) {
                $checkWallet = ContractorWallet::query()
                    ->where('task_id', $task->id)
                    ->get();

                if (blank ($checkWallet)) {
                    if (filled ($task->costs())) {
                        ContractorWallet::query()
                            ->create([
                                'user_id' => $task->assignee_id,
                                'task_id' => $task->id,
                                'amount' => $task->costs()?->sum('cost'),
                            ]);
                    }
                }

                ResolveAssetPreventiveServiceAction::resolve()
                    ->execute($task);
            }

            ResolveSendNotificationAction::resolve()
                ->execute(
                    status: $task->status,
                    task: $task
                );

            Notification\StatusUpdated\SendNotificationAction::resolve()
                ->execute($task->ticket, $task);
        }
    }
}
