<?php

namespace App\Exceptions\Task;

use App\Exceptions\RenderToJson;
use Domain\Shared\Ticket\Models\Task;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class InvalidUpdateStatusException extends \Exception
{
    use RenderToJson;

    public function __construct(public Task $task)
    {
        parent::__construct(
            message: __('Cannot update task, invalid task status is :status', [
                'status' => Str::headline(
                    $this->task->status instanceof \UnitEnum ?
                        $this->task->status->name : $this->task->status
                )
            ]),
            code: Response::HTTP_FORBIDDEN
        );
    }
}
