<?php

namespace Domain\Task\Actions;

use App\Exceptions\Task\InvalidUpdateStatusException;
use Closure;
use App\DataTransferObjects\Task\AcquireStatusData;
use App\DataTransferObjects\Task\TaskNumberData;
use Domain\Shared\Foundation\Action;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;

class UpdateStatusAction extends Action
{
    protected Closure | null $invalidStatus = null;

    protected Closure | null $status = null;

    protected Closure | null $abilities = null;

    protected Closure | null $notification = null;

    protected Closure | null $query = null;

    protected Closure | null $extraFields = null;

    protected Closure | null $after = null;

    public function invalidStatusUsing(Closure | null $callback): static
    {
        $this->invalidStatus = $callback;

        return $this;
    }

    public function statusUsing(Closure | null $callback): static
    {
        $this->status = $callback;

        return $this;
    }

    public function abilitiesUsing(Closure | null $callback): static
    {
        $this->abilities = $callback;

        return $this;
    }

    public function afterUsing(Closure | null $callback): static
    {
        $this->after = $callback;

        return $this;
    }

    public function extraFieldsUsing(Closure | null $callback): static
    {
        $this->extraFields = $callback;

        return $this;
    }

    public function notificationUsing(Closure | null $callback): static
    {
        $this->notification = $callback;

        return $this;
    }

    protected function resolveCallable(Task $task, Closure | null $callback): mixed
    {
        if (! $callback) {
            return false;
        }

        return  $this->container->call($callback, ['task' => $task]);
    }

    public function queryUsing(Closure | null $callback): static
    {
        $this->query = $callback;

        return $this;
    }

    /**
     * @param TaskNumberData $data
     * @param User $user
     * @return Task
     * @throws InvalidUpdateStatusException
     * @throws \App\Exceptions\Task\LockTimeAcquireStatusException
     * @throws \App\Exceptions\Task\UpdateForbiddenException
     */
    public function execute(TaskNumberData $data, User $user): Task
    {
        return value(
            function (Task $task) use ($data, $user) {
                AcquireStatusAction::resolve()
                    ->execute(
                        data: new AcquireStatusData(
                            lockId: $data->taskNumber,
                            abilities: $this->resolveCallable($task, $this->abilities)
                        ),
                        user: $user,
                        task: $task,
                        callback: function (Task $task): void {
                            if ($this->resolveCallable($task, $this->invalidStatus)) {
                                throw new InvalidUpdateStatusException($task);
                            }

                            $task->update([
                                ...Collection::wrap($this->resolveCallable($task, $this->extraFields))
                                    ->filter()
                                    ->toArray(),
                                ...[
                                    'status' => $this->resolveCallable($task, $this->status)
                                ]
                            ]);

                            $this->resolveCallable($task, $this->notification);
                        }
                    );

                $this->resolveCallable($task, $this->after);

                return $task;
            },

            $this->container
                ->call($this->query)
                ->resolveFromTaskNumberData($data)
        );
    }
}
