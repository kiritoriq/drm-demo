<?php

namespace Domain\Notification\Actions;

use App\DataTransferObjects\Notification\CreateNotificationData;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Domain\Shared\User\Models\Role as ModelsRole;
use Infrastructure\Notification\Models\Notification;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class CreateNotificationAction extends Action
{
    public function execute(
        CreateNotificationData $data,
        User | null $user = null,
        Role | null $type = null,
        Role | null $unlessType = null,
    ): Notification {
        return value(
            fn (array $attributes) => $user ?
                $user
                    ->mobileNotifications()
                    ->create($attributes) :

                Notification::query()
                    ->create($attributes),

            attributes: [
                'title' => $data->title,
                'content' => $data->content,
                'role_id' => $this->resolveType($type),
                'unless_role_id' => $this->resolveType($unlessType),
                'image' => $data->image,
                'payload' => $data->payload,
            ],
        );
    }

    protected function resolveType(Role | null $type): null | int
    {
        if (! $type) {
            return null;
        }

        return ModelsRole::query()->resolveType($type);
    }
}