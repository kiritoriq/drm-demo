<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use Domain\Shared\User\Models\User;
use Domain\User\Actions\HydrateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Infrastructure\Filament\RedirectToIndex;

class CreateBranch extends CreateRecord
{
    use RedirectToIndex;

    protected static string $resource = BranchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return HydrateAction::resolve()
            ->execute(
                $data,
                resolve(Authenticatable::class)
            );
    }

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
                    ->title(__('notification.web.settings.branches.created.title'))
                    ->body(__('notification.web.settings.branches.created.content', [
                        'branch_name' => $this->record->name
                    ]))
                    ->success()
                    ->toDatabase()
            );
        }
    }
}
