<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Domain\Shared\User\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Infrastructure\Filament\RedirectToIndex;

class CreateUser extends CreateRecord
{
    use RedirectToIndex;

    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $adminUsers = User::query()
            ->whereRelation(
                'roles',
                fn (Builder $query) => $query->where('name', '=', 'Admin')
            )
            ->get();

        foreach ($adminUsers as $user) {
            $user->notify(
                Notification::make()
                    ->title(__('notification.web.settings.user.created.title'))
                    ->body(__('notification.web.settings.user.created.content', [
                        'user_name' => $this->record->name
                    ]))
                    ->success()
                    ->toDatabase()
            );
        }
    }
}
