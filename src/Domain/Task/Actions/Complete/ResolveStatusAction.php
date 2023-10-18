<?php

namespace Domain\Task\Actions\Complete;

use App\DataTransferObjects\Task\TaskNumberData;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\TaskCompletedReport;
use Domain\Shared\User\Models\User;
use Domain\Task\Actions\Notification;
use Domain\Task\Actions\UpdateStatusAction;
use Domain\Ticket\Enums\Task\JobStatus;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveStatusAction extends Action
{
    public function execute(TaskNumberData $data, User $user, array $additionalData): Task
    {
        $task = UpdateStatusAction::resolve()
            ->queryUsing(fn () => Task::query())
            ->invalidStatusUsing(fn (Task $task) => ! $task->eligibleToComplete())
            ->abilitiesUsing(fn () => 'complete')
            ->statusUsing(fn () => JobStatus::ProgressCompleted->value)
            ->extraFieldsUsing(fn () => [
                'completed_at' => now()
            ])
            ->afterUsing(function (Task $task) use ($additionalData, $user) {
                foreach ($additionalData['completes'] as $complete) {
                    $completed = $task->completedReports()->create([
                        'notes' => $complete['notes'],
                        'user_id' => $user->id
                    ]);

                    $imageData = (array_key_exists('images', $complete) ? $complete['images'] : []);

                    if (filled ($imageData)) {
                        foreach ($imageData as $image) {
                            $completed->addMedia($image)->toMediaCollection(TaskCompletedReport::COLLECTION_NAME);
                        }
                    }
                }

                $task->processStatuses()->create([
                    'title' => 'Job Completed',
                    'description' => 'Work completed and QC',
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
