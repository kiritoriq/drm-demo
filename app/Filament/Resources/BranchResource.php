<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Filament\Resources\BranchResource\RelationManagers\AssetsRelationManager;
use App\Filament\Resources\BranchResource\RelationManagers\UsersRelationManager;
use Domain\Branch\Actions\FetchBranchFormAction;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\Branch;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                ...[
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('user_id')
                                ->label('Owner')
                                ->required()
                                ->when(Role::hasAny([Role::admin, Role::officeAdmin]))
                                ->relationship('owner', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                ],
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AssetsRelationManager::class,
            UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
            'asset-histories' => Pages\AssetHistories::route('assets/{record}/history')
        ];
    }
}
