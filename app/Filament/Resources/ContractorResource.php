<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContractorResource\Actions\VerificationAction;
use App\Filament\Resources\ContractorResource\Pages;
use App\Filament\Resources\ContractorResource\Widgets\CalendarWidget;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Enums\VendorType;
use Domain\Shared\User\Models\User;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Infrastructure\Filament\HasNavigationInteractWithStatus;
use Infrastructure\Filament\InteractWithResourceLabelHasStatus;

class ContractorResource extends Resource
{
    use HasNavigationInteractWithStatus,
        InteractWithResourceLabelHasStatus;

    protected static ?string $model = User::class;

    protected static ?string $label = 'Contractors';

    protected static ?string $navigationLabel = 'Contractors';

    protected static ?string $slug = 'contractors';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Contractors';

    public static function canViewAny(): bool
    {
        return Role::hasAny([Role::admin, Role::officeAdmin, Role::contractor]);
    }

    public static function canView(Model $record): bool
    {
        return Role::hasAny([Role::admin, Role::officeAdmin, Role::contractor]) &&
            static::checkOwnership(
                user: resolve(Authenticatable::class),
                model: $record
            );
    }

    public static function canCreate(): bool
    {
        return Role::hasAny([Role::admin, Role::officeAdmin]);
    }

    public static function canEdit(Model $record): bool
    {
        return Role::hasAny([Role::admin, Role::officeAdmin, Role::contractor]) &&
            static::checkOwnership(
                user: resolve(Authenticatable::class),
                model: $record
            );
    }

    public static function checkOwnership(User $user, Model $model): bool
    {
        if (Role::exactlyContractorRole()) {
            return $user->id === $model->id;
        }

        return true;
    }

    public static function getWidgets(): array
    {
        return [
            CalendarWidget::class
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(columns: 1)
                    ->schema([
                        Select::make('vendor_type')
                            ->label(label: 'Type')
                            ->options(VendorType::getCaseOptions())
                            ->required()
                    ]),
                Section::make(heading: 'Contact Information')
                    ->columns(columns: 2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->required()
                            ->email(),
                        TextInput::make('password')
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                        TextInput::make('phone')
                            ->label(label: 'Contact Number')
                            ->numeric(),
                        TextInput::make('whatsapp_number')
                            ->label(label: 'Whatsapp Number')
                            ->numeric()
                    ]),
                Section::make(heading: 'Business Information')
                    ->columns(columns: 2)
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('business_logo')
                            ->label(label: 'Business Logo')
                            ->collection(User::BUSINESS_MEDIA_COLLECTION_NAME)
                            ->acceptedFileTypes(['image/*'])
                            ->enableDownload()
                            ->enableOpen(),
                        TextInput::make('company_name')
                            ->label(label: 'Business Name'),
                        Textarea::make('company_description')
                            ->label(label: 'Business Description'),
                        Textarea::make('office_address')
                            ->label(label: 'Business Address'),
                        Select::make('services')
                            ->label(label: 'What Services You Offer?')
                            ->relationship(
                                relationshipName: 'offeredServices',
                                titleColumnName: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->multiple(),
                        Select::make('location_coverages')
                            ->label(label: 'Location Coverage')
                            ->relationship(
                                relationshipName: 'locationCoverages',
                                titleColumnName: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->multiple(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('vendor_type')
                    ->enum(VendorType::getCaseOptions()),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(label: 'Contact Number')
                    ->searchable(),
                TextColumn::make('whatsapp_number')
                    ->label(label: 'Whatsapp Number')
                    ->getStateUsing(fn (User $record) => $record->whatsapp_number ?? '-')
                    ->searchable(),
                ToggleColumn::make('status')
                    ->label(label: 'Active Status'),
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
            ->headerActions([
                Action::make(name: 'Task Calendar')
                    ->visible(fn ($livewire) => ! $livewire->status)
                    ->button()
                    ->icon(icon: 'heroicon-o-calendar')
                    ->url(ContractorResource::getUrl('task-calendar'))
                    ->openUrlInNewTab(true)
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make(name: 'View Task Calendar')
                    ->visible(fn (User $record) => $record->isVerified())
                    ->icon(icon: 'heroicon-o-calendar')
                    ->url(fn (User $record) => ContractorResource::getUrl('show-calendar', encrypt($record->id)))
                    ->openUrlInNewTab(condition: true),

                Tables\Actions\Action::make(name: 'Transaction History')
                    ->visible(fn (User $record) => $record->isVerified())
                    ->icon(icon: 'heroicon-o-currency-dollar')
                    ->url(fn (User $record) => ContractorResource::getUrl('transaction-history', $record))
                    ->openUrlInNewTab(condition: true),

                VerificationAction::make()
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
            'index' => Pages\ListContractors::route('/'),
            'create' => Pages\CreateContractor::route('/create'),
            'edit' => Pages\EditContractor::route('/{record}/edit'),
            'task-calendar' => Pages\ViewCalendar::route('/task-calendar'),
            'show-calendar' => Pages\ViewCalendar::route('/task-calendar?contractor_id={record}'),
            'transaction-history' => Pages\TransactionHistory::route('/{record}/transaction-history'),
        ];
    }
}
