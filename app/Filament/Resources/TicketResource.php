<?php

namespace App\Filament\Resources;

use App\DataTransferObjects\User\UpsertCustomerData;
use App\Filament\Resources\TicketResource\Actions\LegendAction;
use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers\LogsRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\MessagesRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\PreventiveServicesRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\QuotationRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\ReportsRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\SiteVisitsRelationManager;
use App\Filament\Resources\TicketResource\RelationManagers\TasksRelationManager;
use Domain\Branch\Actions\FetchBranchFormAction;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\Project;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Builders\UserBuilder;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\Branch;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Actions\ResolveMaxTicketNumberByMonthAction;
use Domain\Ticket\Actions\ResolveTicketTableColorAction;
use Domain\Ticket\Builders\TicketBuilder;
use Domain\Ticket\Enums\Priority;
use Domain\Ticket\Enums\Status;
use Domain\Ticket\ValueObjects\TicketNumber;
use Domain\User\Actions\Customer\UpsertCustomerAction;
use Exception;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Infrastructure\Filament\HasNavigationInteractWithStatus;
use Infrastructure\Filament\InteractWithResourceLabelHasStatus;
use Spatie\Valuestore\Valuestore;

class TicketResource extends Resource
{
    use HasNavigationInteractWithStatus,
        InteractWithResourceLabelHasStatus;

    protected static ?string $model = Ticket::class;

    protected static ?string $label = 'Tickets';

    protected static ?string $navigationLabel = 'All Tickets';

    protected static ?string $modelLabel = 'Ticket';

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make(name: 'ticket_number')
                    ->default(function (callable $get) {
                        $project = Project::find($get('project_id'));

                        $ticketNumber = new TicketNumber(
                            latestNumber: ResolveMaxTicketNumberByMonthAction::resolve()->execute(),
                            date: now(),
                            project: $project
                        );

                        return $ticketNumber->formatted;
                    })
                    ->disabled(),
                DateTimePicker::make(name: 'created_at')
                    ->label(label: 'Date')
                    ->default(now())
                    ->displayFormat('d F, Y')
                    ->dehydrated(false)
                    ->disabled(),

                Section::make(heading: 'Contact Info')
                    ->schema([
                        Select::make(name: 'customer_id')
                            ->label(label: 'Client')
                            ->relationship(
                                relationshipName: 'customer',
                                titleColumnName: 'name',
                                callback: fn (Builder $query) => $query
                                    ->whereRelation(
                                        'roles',
                                        fn (Builder $roleBuilder) => $roleBuilder->where('name', Role::customer->value)
                                    )
                            )
                            ->disabled(fn (Ticket | null $record) => Role::customersRole() || $record)
                            ->default(function () {
                                if (Role::exactlyCustomerRole()) {
                                    return resolve(Authenticatable::class)->id;
                                }
                                
                                if (Role::exactlyBranchCustomerRole()) {
                                    return resolve(Authenticatable::class)->parent_id;
                                }

                                return '';
                            })
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('email')
                                    ->email()
                                    ->unique(ignorable: fn (?Model $record): ?Model => $record),
                                TextInput::make('password')
                                    ->password()
                                    ->required(fn ($livewire) => $livewire instanceof CreateRecord)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                                TextInput::make('company_name')
                                    ->label(label: 'Company Name')
                                    ->maxLength(length: 255),
                                TextInput::make('brand_name')
                                    ->label(label: 'Brand Name')
                                    ->maxLength(length: 255),
                                TextInput::make('phone')
                                    ->numeric()
                                    ->required()
                                    ->unique(ignorable: fn (?Model $record): ?Model => $record),
                                Textarea::make('office_address')
                                    ->label(label: 'Address'),
                            ])
                            ->createOptionUsing(function (array $data) {
                                $customer = UpsertCustomerAction::resolve()
                                    ->execute(data: UpsertCustomerData::resolveFrom($data));

                                return $customer->id;
                            })
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $user = User::find($get('customer_id'));

                                $set('branch_id', null);
                                if ($user) {
                                    $set('company', $user->company_name);
                                    $set('email', $user->id);
                                    $set('phone', $user->id);
                                } else {
                                    $set('company', null);
                                    $set('email', null);
                                    $set('phone', null);
                                }
                            }),
                        TextInput::make(name: 'company')
                            ->dehydrated(false)
                            ->default(fn () => Role::customersRole() ? resolve(Authenticatable::class)->company_name : '')
                            ->disabled(),
                        Select::make('email')
                            ->label(label: 'Customer Email Address (HQ)')
                            ->relationship(
                                relationshipName: 'customer',
                                titleColumnName: 'email',
                                callback: fn (Builder $query) => $query
                                    ->whereRelation(
                                        'roles',
                                        fn (Builder $roleBuilder) => $roleBuilder->where('name', Role::customer->value)
                                    )
                            )
                            ->disabled(fn (Ticket | null $record) => Role::customersRole() || $record)
                            ->default(function () {
                                if (Role::exactlyCustomerRole()) {
                                    return resolve(Authenticatable::class)->id;
                                }
                                
                                if (Role::exactlyBranchCustomerRole()) {
                                    return resolve(Authenticatable::class)->parent_id;
                                }

                                return '';
                            })
                            ->dehydrated(false)
                            ->reactive()
                            ->searchable()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $user = User::query()->find($get('email'));

                                $set('branch_id', null);
                                if ($user) {
                                    $set('company', $user->company_name);
                                    $set('customer_id', $user->id);
                                    $set('phone', $user->id);
                                } else {
                                    $set('company', null);
                                    $set('customer_id', null);
                                    $set('phone', null);
                                }
                            }),
                        Select::make('phone')
                            ->label(label: 'Customer Phone Number (HQ)')
                            ->relationship(
                                relationshipName: 'customer',
                                titleColumnName: 'phone',
                                callback: fn (Builder $query) => $query
                                    ->whereRelation(
                                        'roles',
                                        fn (Builder $roleBuilder) => $roleBuilder->where('name', Role::customer->value)
                                    )
                            )
                            ->disabled(fn (Ticket | null $record) => Role::customersRole() || $record)
                            ->default(function () {
                                if (Role::exactlyCustomerRole()) {
                                    return resolve(Authenticatable::class)->id;
                                }
                                
                                if (Role::exactlyBranchCustomerRole()) {
                                    return resolve(Authenticatable::class)->parent_id;
                                }

                                return '';
                            })
                            ->dehydrated(false)
                            ->reactive()
                            ->searchable()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $user = User::query()->find($get('phone'));

                                $set('branch_id', null);
                                if ($user) {
                                    $set('company', $user->company_name);
                                    $set('customer_id', $user->id);
                                    $set('email', $user->id);
                                } else {
                                    $set('company', null);
                                    $set('customer_id', null);
                                    $set('email', null);
                                }
                            }),
                    //    TextInput::make('email')
                    //        ->label(label: 'Customer Email Address (HQ)')
                    //        ->dehydrated(false)
                    //        ->default(fn () => Role::exactlyCustomerRole() ? resolve(Authenticatable::class)->email : '')
                    //        ->disabled(),
                    //    TextInput::make('phone')
                    //        ->label(label: 'Customer Phone Number (HQ)')
                    //        ->dehydrated(false)
                    //        ->default(fn () => Role::exactlyCustomerRole() ? resolve(Authenticatable::class)->phone : '')
                    //        ->disabled(),
                        Select::make(name: 'branch_id')
                            ->label(label: 'Branch')
                            ->relationship(
                                relationshipName: 'branch',
                                titleColumnName: 'name',
                                callback: fn (Builder $query, callable $get) => $query
                                    ->when(
                                        ! Role::exactlyBranchCustomerRole(),
                                        fn (Builder $builder) => $builder->where('user_id', $get('customer_id'))
                                    )
                                    ->when(
                                        Role::exactlyBranchCustomerRole(),
                                        fn (Builder $builder) => $builder->whereRelation(
                                            'users',
                                            fn (Builder $userBuilder) => $userBuilder->where('user_id', resolve(Authenticatable::class)->id)
                                        )
                                    )
                            )
                            ->createOptionForm(function () {
                                if (! Role::exactlyBranchCustomerRole()) {
                                    return [
                                        ...[
                                            Grid::make(columns: 2)
                                            ->schema([
                                                Select::make('user_id')
                                                    ->label('Owner')
                                                    ->required()
                                                    ->relationship(
                                                        relationshipName: 'owner',
                                                        titleColumnName: 'name',
                                                        callback: fn (Builder $query) => $query->whereRelation(
                                                            relation: 'roles',
                                                            column: fn (Builder $roleBuilder) => $roleBuilder->where('name', Role::customer->value)
                                                        )
                                                    )
                                                    ->default(fn ($livewire) => $livewire->data['customer_id'])
                                                    ->disabled()
                                                    ->preload()
                                                    ->searchable(),
                                            ])
                                        ],
                                        ...FetchBranchFormAction::resolve()->execute()
                                    ];
                                }

                                return [];
                            })
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $outlet = Branch::find($get('branch_id'));

                                $set('assets', null);
                                if ($outlet) {
                                    $set('outlet_pic_name', $outlet->person_in_charge);
                                    $set('outlet_phone', $outlet->phone);
                                } else {
                                    $set('outlet_pic_name', null);
                                    $set('outlet_phone', null);
                                }
                            })
                            ->required(),
                        Select::make(name: 'areas')
                            ->label(label: 'Maintenance Type (Location)')
                            ->relationship('areas', 'name')
                            ->multiple()
                            ->preload(),
                        TextInput::make('outlet_pic_name')
                            ->label(label: 'Outlet In Charge Person')
                            ->dehydrated(false)
                            ->disabled(),
                        TextInput::make('outlet_phone')
                            ->label(label: 'Outlet Phone Number')
                            ->dehydrated(false)
                            ->disabled(),
                    ])
                    ->columns(columns: 2),

                Section::make(heading: 'Ticket Info')
                    ->schema([
                        Select::make(name: 'project_id')
                            ->required()
                            ->relationship(
                                relationshipName: 'project',
                                titleColumnName: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set, Ticket | null $record) {
                                $project = Project::find($get('project_id'));
                                $latestNumber = Ticket::count() + 1;

                                if (filled($record)) {
                                    $latestNumber = intval(explode('-', $record->ticket_number)[1]);
                                }

                                $ticketNumber = new TicketNumber(
                                    latestNumber: $latestNumber,
                                    date: filled($record) ? $record->created_at : now(),
                                    project: $project
                                );

                                return $set('ticket_number', $ticketNumber->formatted);
                            })
                            ->visible(fn () => Role::hasAny([Role::admin, Role::officeAdmin, Role::serviceManager])),

                        Select::make(name: 'raised_by_id')
                            ->label(label: 'Created By')
                            ->required()
                            ->relationship(
                                relationshipName: 'raisedBy',
                                titleColumnName: 'name'
                            )
                            ->default(resolve(Authenticatable::class)->id)
                            ->searchable()
                            ->preload()
                            ->disabled(fn (Ticket | null $record) => Role::doesntHave(Role::admin) || $record),

                        Select::make(name: 'assignee_id')
                            ->relationship(
                                relationshipName: 'assignee',
                                titleColumnName: 'name',
                                callback: fn (UserBuilder $query) => $query->whereCanAssignedToTicket()
                            )
                            ->searchable()
                            ->preload()
                            ->visible(fn () => Role::hasAny([Role::admin, Role::officeAdmin, Role::serviceManager]))
                            ->disabled(function (Ticket | null $record): bool {
                                if (Role::exactlyServiceManagerRole()) {
                                    return $record && filled ($record->assignee_id);
                                }

                                return false;
                            }),
                        
                        Select::make(name: 'backup_assignees')
                            ->label(label: 'Backup Assignee')
                            ->relationship(
                                relationshipName: 'backupAssignees',
                                titleColumnName: 'name',
                                callback: fn (UserBuilder $query) => $query->whereCanAssignedToTicket()
                            )
                            ->searchable()
                            ->preload()
                            ->multiple()
                            ->visible(fn () => Role::hasAny([Role::admin, Role::officeAdmin, Role::serviceManager]))
                            ->disabled(function (Ticket | null $record): bool {
                                if (Role::exactlyServiceManagerRole()) {
                                    return $record && filled ($record->assignee_id);
                                }

                                return false;
                            }),

                        Select::make(name: 'priority')
                            ->options(Priority::getCaseOptions())
                            ->reactive()
                            ->afterStateUpdated(function (callable $get, callable $set) {
                                $settings = Valuestore::make(config('filament-settings.path'));
                                $priority = $get('priority');

                                $date = match ($priority) {
                                    Priority::Low->value => now()->addDays($settings->get('low_priority_due')),
                                    Priority::Medium->value => now()->addDays($settings->get('medium_priority_due')),
                                    Priority::High->value => now()->addDays($settings->get('high_priority_due')),
                                    Priority::Critical->value => now()->addDays($settings->get('critical_priority_due')),
                                };

                                $set('due_at', $date);
                            })
                            ->hidden(fn () => Role::customersRole()),

                        Select::make(name: 'status')
                            ->required()
                            ->reactive()
                            ->default(Status::New->value)
                            ->options(Status::getCaseOptions())
                            ->disabled(fn () => Role::customersRole()),

                        DatePicker::make(name: 'due_at')
                            ->visible(fn () => Role::hasAny([Role::admin, Role::officeAdmin, Role::serviceManager])),

                        Textarea::make(name: 'cancel_reason')
                            ->label(label: 'Reason')
                            ->visible(fn (callable $get) => $get('status') === Status::Cancelled->value)
                            ->required(fn (callable $get) => $get('status') === Status::Cancelled->value)
                    ])
                    ->columns(columns: 3),

                Card::make()
                    ->schema([
                        Group::make([
                            TextInput::make(name: 'subject')
                                ->required()
                                ->maxLength(length: 255),

                            MarkdownEditor::make(name: 'description')
                                ->required()
                                ->disableToolbarButtons([
                                    'codeBlock',
                                    'attachFiles',
                                ]),
                        ]),
                        Group::make([
                            Select::make('ticket_assets')
                                ->label(label: 'Assets')
                                ->relationship(
                                    relationshipName: 'assets',
                                    titleColumnName: 'name',
                                    callback: fn (Builder $query, callable $get) => $query->where('branch_id', $get('branch_id'))
                                )
                                ->searchable()
                                ->preload()
                                ->multiple(),
                            SpatieMediaLibraryFileUpload::make('ticket_images')
                                ->enableDownload()
                                ->enableOpen()
                                ->collection(Ticket::COLLECTION_NAME)
                                ->multiple(),
                        ])
                            ->columns(2),
                    ]),
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\Layout\Split::make(schema: [
                    ColorColumn::make('ticket_color')
                        ->default(fn (Ticket $record) => ResolveTicketTableColorAction::resolve()->execute(ticket: $record))
                        ->grow(false),

                    Tables\Columns\Layout\Stack::make([
                        TextColumn::make(name: 'ticket_number')
                            ->searchable()
                            ->sortable()
                            ->extraAttributes([
                                'style' => 'font-weight: bold'
                            ]),
                        TextColumn::make(name: 'subject')
                            ->getStateUsing(fn (Ticket $record) => 'Title: ' . $record->subject)
                            ->searchable()
                            ->extraAttributes([
                                'style' => 'font-weight: bold'
                            ])
                    ]),
                    
                    TextColumn::make(name: 'description')
                        ->words(words: 7)
                        ->searchable(),
                ]),
                Tables\Columns\Layout\Panel::make([
                    Tables\Columns\Layout\Split::make([
                        Tables\Columns\Layout\Stack::make([
                            TextColumn::make(name: 'assignee.name')
                                ->label(label: 'Assignee')
                                ->getStateUsing(fn (Ticket $record) => 'Assignee')
                                ->extraAttributes([
                                    'style' => 'font-weight:bold'
                                ]),
                            SelectColumn::make(name: 'assignee_id')
                                ->label(label: 'Assignee')
                                ->options(User::query()->whereCanAssignedToTicket()->pluck(column: 'name', key: 'id'))
                                ->visible(fn () => Role::hasAny([Role::admin, Role::officeAdmin])),
                            TextColumn::make(name: 'assignee.name')
                                ->label(label: 'Assignee')
                                ->visible(fn () => ! Role::hasAny([Role::admin, Role::officeAdmin]))
                                ->searchable(),
                        ])->alignment('center'),
                        Tables\Columns\Layout\Stack::make(function ($livewire) {
                            $statusColumn = null;

                            if (Role::hasAny([Role::admin, Role::officeAdmin, Role::serviceManager])) {
                                $statusColumn = SelectColumn::make(name: 'status')
                                    ->label(label: 'Status')
                                    ->options(Status::getCaseOptions());
                            }

                            if (Role::customersRole()) {
                                $statusColumn = TextColumn::make(name: 'status')
                                    ->label(label: 'Status')
                                    ->enum(Status::getCaseOptions())
                                    ->visible(fn () => Role::customersRole())
                                    ->searchable();
                            }

                            return [
                                ...[
                                    TextColumn::make(name: 'status')
                                    ->label(label: 'Status')
                                    ->getStateUsing(fn (Ticket $record) => 'Status')
                                    ->extraAttributes([
                                        'style' => 'font-weight:bold'
                                    ]),
                                ],
                                ...[
                                    $statusColumn
                                ]
                            ];
                        })->alignment('center'),
                        Tables\Columns\Layout\Stack::make([
                            TextColumn::make(name: 'branch.name')
                                ->label(label: 'Branch')
                                ->getStateUsing(fn (Ticket $record) => 'Branch')
                                ->extraAttributes([
                                    'style' => 'font-weight:bold'
                                ]),
                            TextColumn::make(name: 'branch.name')
                                ->label(label: 'Branch')
                                ->searchable(),
                        ])->alignment('center'),
                        Tables\Columns\Layout\Stack::make([
                            TextColumn::make(name: 'due_at')
                                ->getStateUsing(fn (Ticket $record) => 'Due At')
                                ->extraAttributes([
                                    'style' => 'font-weight:bold'
                                ]),
                            TextColumn::make(name: 'due_at')
                                ->date()
                                ->sortable()
                        ])->alignment('center'),
                        Tables\Columns\Layout\Stack::make([
                            TextColumn::make(name: 'updated_at')
                                ->getStateUsing(fn (Ticket $record) => 'Last Update At')
                                ->extraAttributes([
                                    'style' => 'font-weight:bold'
                                ]),
                            TextColumn::make(name: 'updated_at')
                                ->date()
                                ->sortable(),
                        ])->alignment('center'),
                        Tables\Columns\Layout\Stack::make([
                            TextColumn::make(name: 'created_at')
                                ->getStateUsing(fn (Ticket $record) => 'Created At')
                                ->extraAttributes([
                                    'style' => 'font-weight:bold'
                                ]),
                            TextColumn::make(name: 'created_at')
                                ->date()
                                ->sortable(),
                        ])->alignment('center'),
                    ])
                ])->collapsed(false),
            ])
            ->headerActions([
                LegendAction::make()
            ])
            ->defaultSort(
                column: 'created_at',
                direction: 'desc'
            )
            ->filters([
                Tables\Filters\SelectFilter::make('assignee')
                    ->relationship(
                        relationshipName: 'assignee',
                        titleColumnName: 'name',
                        callback: fn (UserBuilder $query) => $query->whereCanAssignedToTicket()
                    )
                    ->searchable(),
                Tables\Filters\SelectFilter::make('customer')
                    ->relationship(
                        relationshipName: 'customer',
                        titleColumnName: 'name',
                        callback: fn (UserBuilder $query) => $query->whereOwnerRole()
                    )
                    ->searchable(),
                Tables\Filters\Filter::make('cancelled_ticket')
                    ->label('Cancelled Ticket')
                    ->visible(fn ($livewire) => blank ($livewire->status))
                    ->query(fn (TicketBuilder $query) => $query->whereCancelledTicket())
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation(condition: true)
                    ->modalHeading(heading: 'Delete Ticket')
                    ->modalSubheading(subheading: 'Are you sure want to delete this ticket?')
                    ->using(function (Ticket $record) {
                        $record->tasks()->delete();

                        $record->delete();

                        Notification::make()
                            ->title('Success')
                            ->body('Ticket deleted successfully.')
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
            ]);
    }

    /**
     * @return array<int, class-string>
     */
    public static function getRelations(): array
    {
        return [
            SiteVisitsRelationManager::class,
            QuotationRelationManager::class,
            TasksRelationManager::class,
            MessagesRelationManager::class,
            ReportsRelationManager::class,
            LogsRelationManager::class,
            PreventiveServicesRelationManager::class
        ];
    }

    /**
     * @return array<string, array<string,string>>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'create-task' => Pages\CreateTask::route('{record}/task/create'),
        ];
    }
}
