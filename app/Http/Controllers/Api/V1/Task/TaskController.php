<?php

namespace App\Http\Controllers\Api\V1\Task;

use App\DataTransferObjects\Task\RejectData;
use App\DataTransferObjects\Task\SearchData;
use App\DataTransferObjects\Task\TaskNumberData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Task\CompleteRequest;
use App\Http\Requests\Task\RejectRequest;
use App\Http\Requests\Task\ReportIssueRequest;
use App\Http\Resources\Task\Collection;
use App\Http\Resources\Task\Resource;
use Domain\Shared\Ticket\Models\Task;
use Domain\Task\Actions;
use Domain\Task\Actions\FetchTaskAction;
use Domain\Task\Actions\FetchTasksAction;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): Responsable
    {
        $tasks = FetchTasksAction::resolve()->execute(
            data: SearchData::resolveFrom( $request->has('status') ? $request->only('status') : ['status' => []]),
            user: $request->user(),
            query: Task::query()
        );

        return new Collection(
            $tasks->paginate()
        );
    }

    public function show(Request $request, Task $task): Responsable
    {
        $fetchAction = FetchTaskAction::resolve()->execute(
            data: TaskNumberData::resolveFrom($task->only('task_number')),
            user: $request->user(),
            query: $task->query()
        );

        return new Resource(
            $fetchAction->first()
        );
    }

    public function accept(Request $request, Task $task): Responsable
    {
        return new Resource(
            Actions\Accept\ResolveStatusAction::resolve()
                ->execute(
                    data: TaskNumberData::resolveFrom($task->only(attributes: 'task_number')),
                    user: $request->user()
                )
        );
    }

    public function reject(RejectRequest $request, Task $task): Responsable
    {
        return new Resource(
            Actions\Failed\ResolveStatusAction::resolve()
                ->execute(
                    taskNumberData: TaskNumberData::resolveFrom($task->only(attributes: 'task_number')),
                    data: RejectData::resolveFrom($request->validated()),
                    user: $request->user()
                )
        );
    }

    public function start(Request $request, Task $task): Responsable
    {
        return new Resource(
            Actions\Start\ResolveStatusAction::resolve()
                ->execute(
                    data: TaskNumberData::resolveFrom($task->only(attributes: 'task_number')),
                    user: $request->user()
                )
        );
    }

    public function complete(CompleteRequest $request, Task $task): Responsable
    {
        return new Resource(
            Actions\Complete\ResolveStatusAction::resolve()
                ->execute(
                    data: TaskNumberData::resolveFrom($task->only(attributes: 'task_number')),
                    user: $request->user(),
                    additionalData: $request->validated()
                )
        );
    }

    public function reportIssue(ReportIssueRequest $request, Task $task): Responsable
    {
        return new Resource(
            Actions\ResolveReportIssueAction::resolve()
                ->execute(
                    taskNumberData: TaskNumberData::resolveFrom($task->only(attributes: 'task_number')),
                    data: $request->validated(),
                    user: $request->user()
                )
        );
    }
}
