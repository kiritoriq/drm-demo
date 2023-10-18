<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Filament\Forms;
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
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $label = 'Customers';

    protected static ?string $navigationLabel = 'Customers';

    protected static ?string $slug = 'customers';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Settings';

    public static function getLabel(): ?string
    {
        return ((resolve(Authenticatable::class) && Role::exactlyCustomerRole()) ? 'Users' : 'Customers');
    }

    protected static function getNavigationLabel(): string
    {
        return ((resolve(Authenticatable::class) && Role::exactlyCustomerRole()) ? 'Users' : 'Customers');
    }

    public static function canViewAny(): bool
    {
        return Role::hasAny([Role::admin, Role::officeAdmin, Role::customer]);
    }

    public static function canView(Model $record): bool
    {
        dd(static::checkOwnership(
            user: resolve(Authenticatable::class),
            model: $record
        ));
        return Role::hasAny([Role::admin, Role::officeAdmin, Role::customer]) &&
            static::checkOwnership(
                user: resolve(Authenticatable::class),
                model: $record
            );
    }

    public static function canCreate(): bool
    {
        return Role::hasAny([Role::admin, Role::officeAdmin, Role::customer]);
    }

    public static function canEdit(Model $record): bool
    {
        return Role::hasAny([Role::admin, Role::officeAdmin, Role::customer]) &&
            static::checkOwnership(
                user: resolve(Authenticatable::class),
                model: $record
            );
    }

    public static function checkOwnership(User $user, Model $model): bool
    {
        if (Role::exactlyCustomerRole()) {
            return $user->id === $model->id || $user->id === $model->parent_id;
        }

        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(name: 'user_type')
                    ->label(label: 'Customer User Type')
                    ->options([
                        'hq_customer' => 'HQ Customer',
                        'branch_customer' => 'Branch Customer'
                    ])
                    ->dehydrated(false)
                    ->visible(fn () => Role::hasAny([Role::admin]))
                    ->reactive(),

                Select::make(name: 'parent_id')
                    ->label(label: 'HQ User')
                    ->relationship(
                        relationshipName: 'parentUser',
                        titleColumnName: 'name'
                    )
                    ->searchable()
                    ->preload()
                    ->default(fn () => Role::exactlyCustomerRole() ? resolve(Authenticatable::class)->id : null)
                    ->disabled(fn () => Role::exactlyCustomerRole())
                    ->visible(fn (callable $get) => ($get('user_type') == 'branch_customer' && Role::hasAny([Role::admin])) || Role::exactlyCustomerRole()),

                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->password()
                    ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                    ->dehydrated(fn ($state) => filled($state))
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                TextInput::make('company_name')
                    ->label(label: 'Company Name')
                    ->default(fn () => Role::exactlyCustomerRole() ? resolve(Authenticatable::class)->company_name : '')
                    ->disabled(fn () => Role::exactlyCustomerRole())
                    ->maxLength(length: 255),
                TextInput::make('brand_name')
                    ->label(label: 'Brand Name')
                    ->default(fn () => Role::exactlyCustomerRole() ? resolve(Authenticatable::class)->brand_name : '')
                    ->disabled(fn () => Role::exactlyCustomerRole())
                    ->maxLength(length: 255),
                Forms\Components\Textarea::make('office_address')
                    ->label(label: 'Address')
                    ->default(fn () => Role::exactlyCustomerRole() ? resolve(Authenticatable::class)->office_address : '')
                    ->disabled(fn () => Role::exactlyCustomerRole()),
                TextInput::make('phone')
                    ->numeric()
                    ->required(),
                // Select::make('roles')
                //     ->relationship(
                //         relationshipName: 'roles',
                //         titleColumnName: 'name',
                //         callback: fn (Builder $query) => $query
                //             ->when(
                //                 Role::exactlyCustomerRole(),
                //                 fn (Builder $builder) => $builder->whereIn('name', [
                //                     Role::branchCustomer->value,
                //                 ])
                //             )
                //             ->when(
                //                 ! Role::exactlyCustomerRole(),
                //                 fn (Builder $builder) => $builder->whereIn('name', [
                //                     Role::customer->value
                //                 ])
                //             )
                //     )
                //     ->multiple()
                //     ->preload(),
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
                TextColumn::make('company_name')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                IconColumn::make('status')
                    ->label(label: 'Active Status')
                    ->boolean(true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort(
                column: 'created_at',
                direction: 'desc'
            )
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
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BranchesRelationManager::class,
            RelationManagers\AttachedBranchesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
