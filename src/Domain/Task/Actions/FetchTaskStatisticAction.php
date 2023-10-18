<?php

namespace Domain\Task\Actions;

use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Task\JobStatus;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchTaskStatisticAction extends Action
{
    public function execute(User $user): array
    {
        $jobStatus = JobStatus::cases();
        $data = [];

        $tasks = Task::query()
            ->where('assignee_id', $user->id)
            ->get();

        foreach ($jobStatus as $status) {
            $data[$status->value]['total'] = $tasks->where('status', $status)->count();
            $data[$status->value]['percentage'] = ($tasks->count() > 0) ? ($data[$status->value]['total']/$tasks->count())*100 : 0;
        }

        return $data;
    }
}
