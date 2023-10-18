<?php

namespace Domain\Task\Actions;

use App\DataTransferObjects\Task\TaskNumberData;
use App\Exceptions\Task\InvalidUpdateStatusException;
use Domain\Shared\Ticket\Models\IssueReport;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Domain\Task\Actions\Notification;
use Domain\Task\Actions\IssueReport\SendNotificationAction;
use Domain\Ticket\Enums\Task\JobStatus;
use Illuminate\Support\Arr;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class ResolveReportIssueAction extends Action
{
    public function execute(
        TaskNumberData $taskNumberData,
        array $data,
        User $user
    ): Task {
        return value (
            function (Task $task) use ($data, $user) {
                if (! $task->eligibleToReportIssue()) {
                    throw new InvalidUpdateStatusException($task);
                }

                foreach ($data['issue_reports'] as $issueReport) {
                    $inputed = $task->issueReports()->create([
                        'issue_report' => $issueReport['title'],
                        'description' => $issueReport['description'] ?? null,
                        'user_id' => $user->id
                    ]);

                    if (array_key_exists('images', $issueReport) && filled ($issueReport['images'])) {
                        foreach ($issueReport['images'] as $image) {
                            $inputed->addMedia($image)->toMediaCollection(IssueReport::COLLECTION_NAME);
                        }
                    }
                }

                $task->costs()->createMany($data['costs']);

                $task->update([
                    'status' => JobStatus::PendingQuotation->value
                ]);

                $task->processStatuses()->create([
                    'title' => 'Site Visit Report Success',
                    'description' => 'Job status changed to Pending Quotation',
                    'status' => $task->status->value
                ]);

                SendNotificationAction::resolve()
                    ->execute($task);

                Notification\SiteVisit\SendNotificationAction::resolve()
                    ->execute(
                        ticket: $task->ticket,
                        task: $task
                    );

                return $task->load(relations: ['issueReports', 'ticket', 'processStatuses', 'costs']);
            },

            task: Task::query()
                ->where('task_number', $taskNumberData->taskNumber)
                ->sole()
        );
    }
}
