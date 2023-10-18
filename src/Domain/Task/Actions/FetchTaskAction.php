<?php

namespace Domain\Task\Actions;

use App\DataTransferObjects\Task\TaskNumberData;
use App\Exceptions\Task\AccessForbiddenException;
use Domain\Shared\User\Models\User;
use Domain\Task\Builders\TaskBuilder;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchTaskAction extends Action
{
    public function execute(
        TaskNumberData $data,
        User $user,
        TaskBuilder $query
    ): TaskBuilder
    {
        $query = $query
            ->whereTaskNumber($data->taskNumber)
            ->with(relations: ['ticket', 'processStatuses', 'issueReports', 'completedReports', 'costs', 'cancelledTask'])
            ->whereAssignedTask($user);

        if ($query->count() <= 0) {
            throw new AccessForbiddenException();
        }

        return $query;
    }
}
