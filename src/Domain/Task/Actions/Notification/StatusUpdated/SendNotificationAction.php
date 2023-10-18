<?php

namespace Domain\Task\Actions\Notification\StatusUpdated;

use App\Filament\Resources\TicketResource;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Task\JobStatus;
use Filament\Notifications\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class SendNotificationAction extends Action
{
    public function execute(Ticket $ticket, Task $task): void
    {
        $adminUsers = User::query()
            ->whereRelation(
                'roles',
                fn (Builder $query) => $query->where('name', '=', 'Admin')
            )
            ->get();

        $branchUsers = $ticket->branch->users;

        if ($task->status === JobStatus::JobAwarded) {
            $recipients = [
                ...$adminUsers,
                ...[
                    $ticket->assignee
                ]
            ];
        } else {
            $recipients = [
                ...$adminUsers,
                ...[
                    $ticket->assignee
                ],
                ...[
                    $ticket->customer,
                ],
                ...[
                    $ticket->raisedBy
                ],
                ...$branchUsers
            ];
        }

        $sentId = [];

        $notification = Notification::make()
            ->title(__('notification.web.task.status_updated.title'))
            ->body(__('notification.web.task.status_updated.content', [
                'task_number' => $task->task_number,
                'status' => Str::headline($task->status->name),
                'date_time' => now()->format('Y-m-d H:i:s')
            ]))
            ->actions([
                NotificationAction::make('view')
                    ->url(TicketResource::getUrl('edit', $ticket))
            ]);

        foreach ($recipients as $user) {
            if (! in_array ($user->id, $sentId)) {
                if (in_array ($task->status, [JobStatus::Failed, JobStatus::QcRejected])) {
                    $sendNotification = $notification
                        ->danger()
                        ->toDatabase();
                } else {
                    $sendNotification = $notification
                        ->success()
                        ->toDatabase();
                }
                
                $user->notify(
                    $sendNotification
                );

                array_push($sentId, $user->id);
            }
        }
    }
}