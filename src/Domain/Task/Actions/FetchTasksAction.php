<?php

namespace Domain\Task\Actions;

use App\DataTransferObjects\Task\SearchData;
use App\Exceptions\Task\InvalidStatusException;
use Domain\Shared\User\Models\User;
use Domain\Task\Builders\TaskBuilder;
use Domain\Ticket\Enums\Task\JobStatus;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchTasksAction extends Action
{
    /**
     * @param SearchData $data
     * @param User $user
     * @return TaskBuilder
     * @throws InvalidStatusException
     */
    public function execute(
        SearchData $data,
        User $user,
        TaskBuilder $query
    ): TaskBuilder
    {
        if (filled ($data->status)) {
            foreach ($data->status as $status) {
                if (blank (JobStatus::make($status))) {
                    throw new InvalidStatusException();
                }
            }
        }

        $statuses = collect($data->status)->map(fn ($status) => JobStatus::make($status));

        return $query
            ->with('ticket')
            ->whereAssignedTask($user)
            ->when(
                filled ($statuses),
                fn (TaskBuilder $query) => $query->whereIn('status', $statuses)
            )
            ->latest('updated_at');
    }
}
