<?php

namespace Domain\Notification\Actions;

use App\Http\Resources\Notification\Collection;
use Domain\Shared\User\Models\User;
use Infrastructure\Notification\Models\Notification;
use Infrastructure\Notification\Tappable\FilterBasedOnUser;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchNotificationsAction extends Action
{
    public function execute(User $user): Collection
    {
        return new Collection(
            resource: Notification::query()
                ->tap(new FilterBasedOnUser($user))
                ->paginate()
        );
    }
}