<?php

namespace Domain\Task\Actions;

use App\DataTransferObjects\Task\AcquireStatusData;
use App\Exceptions\Task\LockTimeAcquireStatusException;
use App\Exceptions\Task\UpdateForbiddenException;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Facades\Cache;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class AcquireStatusAction extends Action
{
    /**
     * @param AcquireStatusData $data
     * @param User $user
     * @param Task $task
     * @param callable $callback
     * @return Task
     * @throws LockTimeAcquireStatusException
     * @throws UpdateForbiddenException
     */
    public function execute(AcquireStatusData $data, User $user, Task $task, callable $callback): Task
    {
        if ($user->cannot(abilities: $data->abilities, arguments: $task)) {
            throw new UpdateForbiddenException();
        }

        try {
            return Cache::lock($data->lockId, seconds: 10)
                ->block(
                    seconds: 5,
                    callback: fn () => tap($task, fn () => $callback($task))
                );
        } catch (LockTimeoutException $e) {
            throw new LockTimeAcquireStatusException($task);
        }
    }
}
