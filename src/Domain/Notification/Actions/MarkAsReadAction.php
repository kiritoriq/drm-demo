<?php

namespace Domain\Notification\Actions;

use Domain\Shared\User\Models\User;
use Infrastructure\Notification\Models\Notification;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class MarkAsReadAction extends Action
{
    public function execute(Notification $notification, User $user)
    {
        $notification->reads()->updateOrCreate(
            attributes: [
                'user_id' => $user->id,
            ],
            values: [
                'read_at' => now(),
            ]
        );
    }
}