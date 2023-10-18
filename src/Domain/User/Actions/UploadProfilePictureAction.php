<?php

namespace Domain\User\Actions;

use Domain\Shared\User\Models\User;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class UploadProfilePictureAction extends Action
{
    public function execute(User $user, array $data): User
    {
        foreach ($user->getMedia(User::COLLECTION_NAME) as $media) {
            $media->delete();
        }

        $user->addMediaFromRequest('profile_image')->toMediaCollection(User::COLLECTION_NAME);

        return $user->refresh();
    }
}