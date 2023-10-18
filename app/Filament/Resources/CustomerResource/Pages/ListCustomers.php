<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Domain\Shared\User\Enums\Role;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getTableQuery(): Builder
    {
        $query = static::getResource()::getEloquentQuery();

        return $query
            ->whereRelation(
                'roles',
                fn (Builder $query) => $query->where('name', Role::customer->value)
            )
            ->when(
                Role::exactlyCustomerRole(),
                fn (Builder $query) => $query->where('id', resolve(Authenticatable::class)->id)->orWhere('parent_id', resolve(Authenticatable::class)->id)
            );
    }
}
