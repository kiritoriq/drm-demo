<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use App\Filament\Resources\TicketResource;
use App\Filament\Resources\TicketResource\Actions\Task\AddReviewAction;
use App\Filament\Resources\TicketResource\Actions\Task\ViewReviewAction;
use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\IssueReport;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\TaskCompletedReport;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Builders\UserBuilder;
use Domain\Shared\User\Enums\Role;
use Domain\Task\Actions\DeleteTaskAction;
use Domain\Task\Actions;
use Domain\Ticket\Enums\Task\JobStatus;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\TextInput::make('task_number')
                            ->default(str_pad(Task::count() + 1, 6, '0', STR_PAD_LEFT))
                            ->disabled(),
                        Select::make(name: 'assignee_id')
                            ->label(label: 'Contractor')
                            ->relationship(
                                relationshipName: 'assignee',
                                titleColumnName: 'name',
                                callback: fn (UserBuilder $query) => $query->whereContractorRole()
                            )
                            ->reactive()
                            ->afterStateUpdated(fn (callable $get, callable $set) => $set('user_id', $get('assignee_id')))
                            ->searchable()
                            ->preload(),
                        Select::make(name: 'status')
                            ->options(JobStatus::getCaseOptions())
                            ->default(JobStatus::New->value)
                            ->searchable()
                            ->preload(),
                    ]),

                Forms\Components\Group::make([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('description'),
                ]),

                Forms\Components\Group::make([
                    Forms\Components\DateTimePicker::make('date_time')
                        ->label(label: 'Start Date and Time')
                        ->required(),

                    Forms\Components\DatePicker::make('due_date')
                        ->label(label: 'Due Date')
                        ->required(),

                    Select::make('assets')
                        ->label(label: 'Assets')
                        ->relationship(
                            relationshipName: 'assets',
                            titleColumnName: 'name',
                            callback: fn (Builder $query, $livewire) => $query->where('branch_id', $livewire->ownerRecord->branch_id)
                        )
                        ->searchable()
                        ->preload()
                        ->multiple(),
                ]),

                // Forms\Components\TextInput::make('cost')
                //     ->label(label: 'Cost')
                //     ->prefix('RM')
                //     ->numeric()
                //     ->default(0)
                //     ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                //         ->numeric()
                //         ->decimalPlaces(places: 2)
                //     ),

                Forms\Components\SpatieMediaLibraryFileUpload::make(Task::COLLECTION_NAME)
                    ->label(label: 'Attachments')
                    ->collection(Task::COLLECTION_NAME)
                    ->enableDownload()
                    ->enableOpen()
                    ->multiple(),

                Forms\Components\Textarea::make(name: 'reject_reason')
                    ->label(label: 'Reject Reason')
                    ->dehydrated(false)
                    ->visible(fn (Task | null $record) => $record ? $record->cancelledTask()?->exists() : false)
                    ->disabled(),

                Section::make('Costs')
                    ->schema([
                        Repeater::make(name: 'costs')
                            ->defaultItems(0)
                            ->relationship()
                            ->schema([
                                Grid::make(columns: 2)
                                    ->schema([
                                        Forms\Components\Textarea::make(name: 'description')
                                            ->required(),

                                        Forms\Components\TextInput::make('cost')
                                            ->label(label: 'Cost')
                                            ->prefix('RM')
                                            ->numeric()
                                            ->default(0)
                                            ->required()
                                            ->mask(
                                                fn (Forms\Components\TextInput\Mask $mask) => $mask
                                                    ->numeric()
                                                    ->decimalPlaces(places: 2)
                                            ),
                                    ])
                            ]),
                    ]),

                Section::make(heading: 'Site Visits')
                    ->schema([
                        Repeater::make(name: 'issueReports')
                            ->defaultItems(0)
                            ->relationship()
                            ->schema([
                                Grid::make(columns: 1)
                                    ->schema([
                                        Select::make(name: 'user_id')
                                            ->label(label: 'Contractor')
                                            ->relationship(
                                                relationshipName: 'assignee',
                                                titleColumnName: 'name',
                                                callback: fn (UserBuilder $query) => $query->whereContractorRole()
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                    ]),
                                Grid::make(columns: 2)
                                    ->schema([
                                        Forms\Components\SpatieMediaLibraryFileUpload::make(IssueReport::COLLECTION_NAME)
                                            ->label(label: 'Site Visit Attachments')
                                            ->collection(IssueReport::COLLECTION_NAME)
                                            ->enableDownload()
                                            ->enableOpen()
                                            ->multiple(),
                                        // ->visible(fn (Task | null $record) => $record ? $record->issueReports()?->exists() : false),

                                        Forms\Components\Textarea::make(name: 'issue_report')
                                            ->label(label: 'Site Visit Report'),
                                        // ->visible(fn (Task | null $record) => $record ? $record->issueReports()?->exists() : false),
                                    ])
                            ])
                    ]),

                Section::make(heading: 'After Fixed')
                    ->schema([
                        Repeater::make('completedReports')
                            ->defaultItems(0)
                            ->relationship()
                            ->schema([
                                Grid::make(columns: 1)
                                    ->schema([
                                        Select::make(name: 'user_id')
                                            ->label(label: 'Contractor')
                                            ->relationship(
                                                relationshipName: 'user',
                                                titleColumnName: 'name',
                                                callback: fn (UserBuilder $query) => $query->whereContractorRole()
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                    ]),
                                Grid::make(columns: 2)
                                    ->schema([
                                        Forms\Components\SpatieMediaLibraryFileUpload::make(TaskCompletedReport::COLLECTION_NAME)
                                            ->label(label: 'After Fix Attachments')
                                            ->collection(TaskCompletedReport::COLLECTION_NAME)
                                            ->enableDownload()
                                            ->enableOpen()
                                            ->multiple(),
                                        // ->visible(fn (Task | null $record) => $record ? $record->isCompleted() : false),

                                        Forms\Components\Textarea::make(name: 'notes')
                                            ->label(label: 'After Fix Notes')
                                        // ->visible(fn (Task | null $record) => $record ? $record->isCompleted() : false)
                                    ]),
                            ])
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('task_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('assignee.name')
                    ->label(label: 'Contractor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->description(description: fn (Task $record): string => filled($record->description) ? Str::descriptionText($record->description) : '', position: 'below')
                    ->searchable(),
                Tables\Columns\TextColumn::make('task_cost')
                    ->money(currency: 'myr', shouldConvert: true),
                Tables\Columns\TextColumn::make('status')
                    ->enum(JobStatus::getCaseOptions()),
                Tables\Columns\TextColumn::make('date_time')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date(),
                IconColumn::make('is_reviewed')
                    ->label(label: 'Is Reviewed')
                    ->boolean()
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\Action::make(name: 'create-task')
                //     ->button()
                //     ->label(label: 'Create Task')
                //     ->url(fn ($livewire) => TicketResource::getUrl('create-task', $livewire->ownerRecord)),
                Tables\Actions\CreateAction::make()
                    ->steps([
                        Forms\Components\Wizard\Step::make(label: 'Task Info & Cost')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('task_number')
                                            ->default(str_pad(Task::count() + 1, 6, '0', STR_PAD_LEFT))
                                            ->disabled(),
                                        Select::make(name: 'assignee_id')
                                            ->label(label: 'Contractor')
                                            ->relationship(
                                                relationshipName: 'assignee',
                                                titleColumnName: 'name',
                                                callback: fn (UserBuilder $query) => $query->whereContractorRole()
                                            )
                                            ->reactive()
                                            ->afterStateUpdated(fn (callable $get, callable $set) => $set('user_id', $get('assignee_id')) )
                                            ->searchable()
                                            ->preload(),
                                        Select::make(name: 'status')
                                            ->options(JobStatus::getCaseOptions())
                                            ->default(JobStatus::New->value)
                                            ->searchable()
                                            ->preload(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Group::make([
                                            Forms\Components\TextInput::make('title')
                                                ->required()
                                                ->maxLength(255),

                                            Forms\Components\Textarea::make('description'),
                                        ]),

                                        Forms\Components\Group::make([
                                            Forms\Components\DateTimePicker::make('date_time')
                                                ->label(label: 'Start Date and Time')
                                                ->required(),

                                            Forms\Components\DatePicker::make('due_date')
                                                ->label(label: 'Due Date')
                                                ->required(),

                                            Select::make('assets')
                                                ->label(label: 'Assets')
                                                ->relationship(
                                                    relationshipName: 'assets',
                                                    titleColumnName: 'name',
                                                    callback: fn (Builder $query, $livewire) => $query->where('branch_id', $livewire->ownerRecord->branch_id)
                                                )
                                                ->searchable()
                                                ->preload()
                                                ->multiple(),
                                        ]),

                                        Forms\Components\SpatieMediaLibraryFileUpload::make(Task::COLLECTION_NAME)
                                            ->label(label: 'Attachments')
                                            ->collection(Task::COLLECTION_NAME)
                                            ->enableDownload()
                                            ->enableOpen()
                                            ->multiple()
                                            ->imagePreviewHeight(150),

                                        Forms\Components\Textarea::make(name: 'reject_reason')
                                            ->label(label: 'Reject Reason')
                                            ->dehydrated(false)
                                            ->visible(fn (Task | null $record) => $record ? $record->cancelledTask()?->exists() : false)
                                            ->disabled(),
                                    ]),

                                Forms\Components\Grid::make(1)
                                    ->schema([
                                        Repeater::make(name: 'costs')
                                            ->defaultItems(0)
                                            ->relationship()
                                            ->grid(columns: 3)
                                            ->schema([
                                                Grid::make(columns: 2)
                                                    ->schema([
                                                        Forms\Components\Textarea::make(name: 'description')
                                                            ->required(),

                                                        Forms\Components\TextInput::make('cost')
                                                            ->label(label: 'Cost')
                                                            ->prefix('RM')
                                                            ->numeric()
                                                            ->default(0)
                                                            ->required()
                                                            ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                                                                ->numeric()
                                                                ->decimalPlaces(places: 2)
                                                            ),
                                                    ])
                                            ]),
                                    ])
                            ]),

                        Forms\Components\Wizard\Step::make(label: 'Site Visits & After Fixed')
                            ->schema([
                                Repeater::make(name: 'issueReports')
                                    ->defaultItems(0)
                                    ->relationship()
                                    ->grid(columns: 2)
                                    ->schema([
                                        Grid::make(columns: 1)
                                            ->schema([
                                                Select::make(name: 'user_id')
                                                    ->label(label: 'Contractor')
                                                    ->relationship(
                                                        relationshipName: 'assignee',
                                                        titleColumnName: 'name',
                                                        callback: fn (UserBuilder $query) => $query->whereContractorRole()
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                ]),
                                        Grid::make(columns: 2)
                                            ->schema([
                                                Forms\Components\SpatieMediaLibraryFileUpload::make(IssueReport::COLLECTION_NAME)
                                                    ->label(label: 'Site Visit Attachments')
                                                    ->collection(IssueReport::COLLECTION_NAME)
                                                    ->enableDownload()
                                                    ->enableOpen()
                                                    ->multiple()
                                                    ->imagePreviewHeight(150),
                                                    // ->visible(fn (Task | null $record) => $record ? $record->issueReports()?->exists() : false),

                                                Forms\Components\Textarea::make(name: 'issue_report')
                                                    ->label(label: 'Site Visit Report'),
                                                    // ->visible(fn (Task | null $record) => $record ? $record->issueReports()?->exists() : false),
                                            ])
                                    ]),

                                Repeater::make('completedReports')
                                    ->defaultItems(0)
                                    ->relationship()
                                    ->grid(columns: 2)
                                    ->schema([
                                        Grid::make(columns: 1)
                                            ->schema([
                                                Select::make(name: 'user_id')
                                                    ->label(label: 'Contractor')
                                                    ->relationship(
                                                        relationshipName: 'user',
                                                        titleColumnName: 'name',
                                                        callback: fn (UserBuilder $query) => $query->whereContractorRole()
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                ]),
                                        Grid::make(columns: 2)
                                            ->schema([
                                                Forms\Components\SpatieMediaLibraryFileUpload::make(TaskCompletedReport::COLLECTION_NAME)
                                                    ->label(label: 'After Fix Attachments')
                                                    ->collection(TaskCompletedReport::COLLECTION_NAME)
                                                    ->enableDownload()
                                                    ->enableOpen()
                                                    ->multiple()
                                                    ->imagePreviewHeight(150),
                                                    // ->visible(fn (Task | null $record) => $record ? $record->isCompleted() : false),

                                                Forms\Components\Textarea::make(name: 'notes')
                                                    ->label(label: 'After Fix Notes')
                                                    // ->visible(fn (Task | null $record) => $record ? $record->isCompleted() : false)
                                            ]),
                                    ])
                            ]),
                    ])
                    ->after(function (Task $record) {
                        $taskNumber = str_pad(
                            string: (string) $record->id,
                            length: 6,
                            pad_string: '0',
                            pad_type: STR_PAD_LEFT
                        );

                        $record->update([
                            'task_number' => $taskNumber
                        ]);

                        $recordMedias = $record->ticket->getMedia(Ticket::COLLECTION_NAME);

                        foreach ($recordMedias as $media) {
                            $media->copy($record, Task::COLLECTION_NAME, 'public');
                        }

                        activity()
                            ->causedBy(resolve(Authenticatable::class)->id)
                            ->performedOn($record->ticket)
                            ->withProperties([
                                'attributes' => [
                                    'task_number' => $taskNumber,
                                    'title' => $record->title,
                                    'date_time' => $record->date_time
                                ]
                            ])
                            ->event('task created')
                            ->log(description: 'Task with number ' . $taskNumber . ' has been created');

                        Actions\New\SendNotificationAction::resolve()
                            ->execute($record->refresh());
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->steps([
                        Forms\Components\Wizard\Step::make(label: 'Task Info & Cost')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('task_number')
                                            ->default(str_pad(Task::count() + 1, 6, '0', STR_PAD_LEFT))
                                            ->disabled(),
                                        Select::make(name: 'assignee_id')
                                            ->label(label: 'Contractor')
                                            ->relationship(
                                                relationshipName: 'assignee',
                                                titleColumnName: 'name',
                                                callback: fn (UserBuilder $query) => $query->whereContractorRole()
                                            )
                                            ->reactive()
                                            ->afterStateUpdated(fn (callable $get, callable $set) => $set('user_id', $get('assignee_id')))
                                            ->searchable()
                                            ->preload(),
                                        Select::make(name: 'status')
                                            ->options(JobStatus::getCaseOptions())
                                            ->default(JobStatus::New->value)
                                            ->searchable()
                                            ->preload(),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Group::make([
                                            Forms\Components\TextInput::make('title')
                                                ->required()
                                                ->maxLength(255),

                                            Forms\Components\Textarea::make('description'),
                                        ]),

                                        Forms\Components\Group::make([
                                            Forms\Components\DateTimePicker::make('date_time')
                                                ->label(label: 'Start Date and Time')
                                                ->required(),

                                            Forms\Components\DatePicker::make('due_date')
                                                ->label(label: 'Due Date')
                                                ->required(),

                                            Select::make('assets')
                                                ->label(label: 'Assets')
                                                ->relationship(
                                                    relationshipName: 'assets',
                                                    titleColumnName: 'name',
                                                    callback: fn (Builder $query, $livewire) => $query->where('branch_id', $livewire->ownerRecord->branch_id)
                                                )
                                                ->searchable()
                                                ->preload()
                                                ->multiple(),
                                        ]),

                                        Forms\Components\SpatieMediaLibraryFileUpload::make(Task::COLLECTION_NAME)
                                            ->label(label: 'Attachments')
                                            ->collection(Task::COLLECTION_NAME)
                                            ->enableDownload()
                                            ->enableOpen()
                                            ->multiple()
                                            ->imagePreviewHeight(150),

                                        Forms\Components\Textarea::make(name: 'reject_reason')
                                            ->label(label: 'Reject Reason')
                                            ->dehydrated(false)
                                            ->visible(fn (Task | null $record) => $record ? $record->cancelledTask()?->exists() : false)
                                            ->disabled(),
                                    ]),

                                Forms\Components\Grid::make(1)
                                    ->schema([
                                        Repeater::make(name: 'costs')
                                            ->defaultItems(0)
                                            ->relationship()
                                            ->grid(columns: 3)
                                            ->schema([
                                                Forms\Components\Textarea::make(name: 'description')
                                                    ->required(),

                                                Forms\Components\TextInput::make('cost')
                                                    ->label(label: 'Cost')
                                                    ->prefix('RM')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->required()
                                                    ->mask(
                                                        fn (Forms\Components\TextInput\Mask $mask) => $mask
                                                            ->numeric()
                                                            ->decimalPlaces(places: 2)
                                                    ),
                                            ]),
                                    ])
                            ]),

                        Forms\Components\Wizard\Step::make(label: 'Site Visits & After Fixed')
                            ->schema([
                                Repeater::make(name: 'issueReports')
                                    ->defaultItems(0)
                                    ->relationship()
                                    ->grid(columns: 2)
                                    ->schema([
                                        Grid::make(columns: 1)
                                            ->schema([
                                                Select::make(name: 'user_id')
                                                    ->label(label: 'Contractor')
                                                    ->relationship(
                                                        relationshipName: 'assignee',
                                                        titleColumnName: 'name',
                                                        callback: fn (UserBuilder $query) => $query->whereContractorRole()
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                            ]),
                                        Grid::make(columns: 2)
                                            ->schema([
                                                Forms\Components\SpatieMediaLibraryFileUpload::make(IssueReport::COLLECTION_NAME)
                                                    ->label(label: 'Site Visit Attachments')
                                                    ->collection(IssueReport::COLLECTION_NAME)
                                                    ->enableDownload()
                                                    ->enableOpen()
                                                    ->multiple()
                                                    ->imagePreviewHeight(150),
                                                // ->visible(fn (Task | null $record) => $record ? $record->issueReports()?->exists() : false),

                                                Forms\Components\Textarea::make(name: 'issue_report')
                                                    ->label(label: 'Site Visit Report'),
                                                // ->visible(fn (Task | null $record) => $record ? $record->issueReports()?->exists() : false),
                                            ])
                                    ]),

                                Repeater::make('completedReports')
                                    ->defaultItems(0)
                                    ->relationship()
                                    ->grid(columns: 2)
                                    ->schema([
                                        Grid::make(columns: 1)
                                            ->schema([
                                                Select::make(name: 'user_id')
                                                    ->label(label: 'Contractor')
                                                    ->relationship(
                                                        relationshipName: 'user',
                                                        titleColumnName: 'name',
                                                        callback: fn (UserBuilder $query) => $query->whereContractorRole()
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                            ]),
                                        Grid::make(columns: 2)
                                            ->schema([
                                                Forms\Components\SpatieMediaLibraryFileUpload::make(TaskCompletedReport::COLLECTION_NAME)
                                                    ->label(label: 'After Fix Attachments')
                                                    ->collection(TaskCompletedReport::COLLECTION_NAME)
                                                    ->enableDownload()
                                                    ->enableOpen()
                                                    ->multiple()
                                                    ->imagePreviewHeight(150),
                                                // ->visible(fn (Task | null $record) => $record ? $record->isCompleted() : false),

                                                Forms\Components\Textarea::make(name: 'notes')
                                                    ->label(label: 'After Fix Notes')
                                                // ->visible(fn (Task | null $record) => $record ? $record->isCompleted() : false)
                                            ]),
                                    ])
                            ]),
                    ])
                    ->skippableSteps(),
                AddReviewAction::make(),
                ViewReviewAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (Task $record) => $record->isCompleted())
                    ->using(function (Task $record) {
                        DeleteTaskAction::resolve()
                            ->execute(task: $record);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->using(function ($records) {
                        foreach ($records as $record) {
                            DeleteTaskAction::resolve()
                                ->execute(task: $record);
                        }
                    })
                    ->hidden(fn () => !Role::hasAny([Role::admin])),
            ]);
    }
}
