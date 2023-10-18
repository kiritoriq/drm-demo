<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Domain\Shared\User\Tappable\Roles\ResolveRoleSelections;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Phpsa\FilamentPasswordReveal\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Settings';

    protected static function shouldRegisterNavigation(): bool
    {
        if (Role::has(Role::customer) && (Role::doesntHave(Role::admin) || Role::doesntHave(Role::officeAdmin))) {
            return false;
        }

        return Role::hasAny([Role::admin, Role::officeAdmin]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->required()
                    ->email()
                    ->unique(ignorable: fn (?Model $record): ?Model => $record),
                Password::make('password')
                    ->revealable()
                    ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                Select::make('roles')
                    ->relationship(
                        relationshipName: 'roles',
                        titleColumnName: 'name',
                        callback: fn (Builder $query) => $query->tap(new ResolveRoleSelections)
                    )
                    ->multiple()
                    ->preload(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->searchable(),
                IconColumn::make('status')
                    ->label(label: 'Active Status')
                    ->boolean(true),
                TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                TernaryFilter::make('status')
                    ->placeholder(placeholder: 'All Users')
                    ->trueLabel(trueLabel: 'Active Users')
                    ->falseLabel(falseLabel: 'Inactive Users')
                    ->queries(
                        true: fn (Builder $query) => $query->whereStatusActive(),
                        false: fn (Builder $query) => $query->whereStatusInactive(),
                        blank: fn (Builder $query) => $query
                    )
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
