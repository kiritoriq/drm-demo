<?php

namespace Domain\User\Actions;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class HydrateAction extends Action
{
    public function execute(array $data, User $user): array
    {
        if (! Role::has(Role::admin)) {
            $data['user_id'] = $user->id;
        }

        return $data;
    }
}
