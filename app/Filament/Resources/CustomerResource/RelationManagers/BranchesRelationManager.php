<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Domain\Branch\Actions\FetchBranchFormAction;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\Branch;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class BranchesRelationManager extends RelationManager
{
    protected static string $relationship = 'branches';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return $ownerRecord->hasRole(Role::customer->value);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...FetchBranchFormAction::resolve()->execute()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('owner.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->words(6)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
