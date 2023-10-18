<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Filament\Resources\ContractorResource;
use Domain\Shared\User\Enums\Role;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class ListContractors extends ListRecords
{
    protected static string $resource = ContractorResource::class;

    public string | null $status = null;

    public function getQueryString()
    {
        return parent::getQueryString() + ['status' => ['except' => '']];
    }

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
                fn (Builder $query) => $query->where('name', Role::contractor->value)
            )
            ->when(
                Role::exactlyContractorRole(),
                fn (Builder $query) => $query->where('id', resolve(Authenticatable::class)->id)
            )
            ->when(
                $this->status,
                fn (Builder $query) => $query->whereNull('verified_at')
            );
    }
}
