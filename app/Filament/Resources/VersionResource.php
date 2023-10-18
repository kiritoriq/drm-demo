<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VersionResource\Pages;
use App\Filament\Resources\VersionResource\RelationManagers;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\Version\Enums\ApplicationType;
use Domain\Shared\Version\Models\Version;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VersionResource extends Resource
{
    protected static ?string $model = Version::class;

    protected static ?string $navigationIcon = 'heroicon-o-device-mobile';

    protected static ?string $navigationGroup = 'Versions';

    public static function canViewAny(): bool
    {
        return Role::hasAny([Role::admin]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        Select::make('application_type')
                            ->options(ApplicationType::getCaseOptions())
                            ->required(),

                        TextInput::make('major_version')
                            ->required(),

                        TextInput::make('minor_version')
                            ->required(),

                        TextInput::make('patch_version')
                            ->required(),

                        Toggle::make('status'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application_type')
                    ->enum(ApplicationType::getCaseOptions())
                    ->searchable(),
                TextColumn::make('version'),
                ToggleColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVersions::route('/'),
            'create' => Pages\CreateVersion::route('/create'),
            'edit' => Pages\EditVersion::route('/{record}/edit'),
        ];
    }    
}
