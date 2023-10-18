<?php

namespace App\Filament\Resources\BranchResource\Pages;

use App\Filament\Resources\BranchResource;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Tappable\FilterForOwnUser;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListBranches extends ListRecords
{
    protected static string $resource = BranchResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return static::getResource()::getEloquentQuery()
            ->when(
                ! Role::has(Role::admin),
                fn (Builder $query) => $query->tap(new FilterForOwnUser)
            );
    }
}
