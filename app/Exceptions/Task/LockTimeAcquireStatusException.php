<?php

namespace App\Exceptions\Task;

use App\Exceptions\RenderToJson;
use Domain\Shared\Ticket\Models\Task;
use Illuminate\Http\Response;

class LockTimeAcquireStatusException extends \Exception
{
    use RenderToJson;

    public function __construct(public Task $task)
    {
        parent::__construct(
            message: __('Can\'t update task with task number :task_number at this time, another request on processing.', [
                'task_number' => $this->task->task_number
            ]),
            code: Response::HTTP_BAD_REQUEST
        );
    }
}
