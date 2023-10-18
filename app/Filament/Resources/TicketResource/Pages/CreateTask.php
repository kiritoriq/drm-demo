<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Domain\Shared\Ticket\Models\IssueReport;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\Ticket\Models\TaskCompletedReport;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Builders\UserBuilder;
use Domain\Shared\User\Models\BranchAsset;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Task\JobStatus;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;

class CreateTask extends Page
{
    use InteractsWithRecord,
        InteractsWithForms;

    protected static string $resource = TicketResource::class;

    protected static string $view = 'filament.resources.ticket-resource.pages.create-task';

    public $taskNumber;

    public $assigneeId;

    public $taskStatus;

    public $taskTitle;

    public $taskDescription;

    public $dateTime;

    public $dueDate;

    public $taskAssets;

    public $taskAttachments;

    public $rejectReason;

    public $costDescription;

    public $costValue;

    public $siteVisitUserId;

    public $issueReportImages;

    public $issueReport;

    public $reportUserId;

    public $taskCompletedReport;

    public $completedReport;

    public function mount(Ticket $record)
    {
        return $this->taskForm->fill([
            'taskNumber' => str_pad(Task::count() + 1, 6, '0', STR_PAD_LEFT)
        ]);
    }

    protected function getCreateTaskSchema()
    {
        $ticket = $this->getRecord();

        return [
            Forms\Components\Wizard::make()
                ->schema(components: [
                    Forms\Components\Wizard\Step::make(label: 'Task Info & Cost')
                        ->schema([
                            Forms\Components\Card::make(schema: [
                                Forms\Components\Section::make(heading: 'Task Details')
                                    ->schema(components: [
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('taskNumber')
                                                    ->disabled(),
                                                Forms\Components\Select::make(name: 'assigneeId')
                                                    ->label(label: 'Contractor')
                                                    ->relationship(
                                                        relationshipName: 'assignee',
                                                        titleColumnName: 'name',
                                                        callback: fn (UserBuilder $query) => $query->whereContractorRole()
                                                    )
                                                    ->reactive()
                                                    ->afterStateUpdated(fn (callable $get, callable $set) => $set('user_id', $get('assigneeId')))
                                                    ->searchable()
                                                    ->preload(),
                                                Forms\Components\Select::make(name: 'taskStatus')
                                                    ->options(JobStatus::getCaseOptions())
                                                    ->default(JobStatus::New->value)
                                                    ->searchable()
                                                    ->preload(),
                                            ]),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Group::make([
                                                    Forms\Components\TextInput::make('taskTitle')
                                                        ->required()
                                                        ->maxLength(255),

                                                    Forms\Components\Textarea::make('taskDescription'),
                                                ]),

                                                Forms\Components\Group::make([
                                                    Forms\Components\DateTimePicker::make('dateTime')
                                                        ->label(label: 'Start Date and Time')
                                                        ->required(),

                                                    Forms\Components\DatePicker::make('dueDate')
                                                        ->label(label: 'Due Date')
                                                        ->required(),

                                                    Forms\Components\Select::make('taskAssets')
                                                        ->label(label: 'Assets')
                                                        ->options(
                                                            BranchAsset::query()
                                                                ->where('branch_id', $ticket->branch_id)
                                                                ->pluck('name', 'id')
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

                                                Forms\Components\FileUpload::make('taskAttachments')
                                                    ->label(label: 'Attachments')
                                                    ->enableDownload()
                                                    ->enableOpen()
                                                    ->multiple(),

                                                Forms\Components\Textarea::make(name: 'rejectReason')
                                                    ->label(label: 'Reject Reason')
                                                    ->dehydrated(false)
                                                    ->visible(fn (Task | null $record) => $record ? $record->cancelledTask()?->exists() : false)
                                                    ->disabled(),
                                            ]),
                                    ]),

                                Forms\Components\Section::make(heading: 'Task Costs')
                                    ->schema(components: [
                                        Forms\Components\Grid::make(1)
                                            ->schema([
                                                Forms\Components\Repeater::make(name: 'costs')
                                                    ->defaultItems(0)
                                                    ->schema([
                                                        Forms\Components\Grid::make(columns: 2)
                                                            ->schema([
                                                                Forms\Components\Textarea::make(name: 'costDescription')
                                                                    ->required(),

                                                                Forms\Components\TextInput::make('costValue')
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
                                            ])
                                    ])
                            ]),
                        ]),

                    Forms\Components\Wizard\Step::make(label: 'Site Visits & After Fixed')
                        ->schema([
                            Forms\Components\Card::make(schema: [
                                Forms\Components\Section::make(heading: 'Issue Report')
                                    ->schema(components: [
                                        Forms\Components\Repeater::make(name: 'issueReports')
                                            ->defaultItems(0)
                                            ->schema([
                                                Forms\Components\Grid::make(columns: 1)
                                                    ->schema([
                                                        Forms\Components\Select::make(name: 'siteVisitUserId')
                                                            ->label(label: 'Contractor')
                                                            ->options(User::query()->whereContractorRole()->pluck('name', 'id')->toArray())
                                                            ->searchable()
                                                            ->preload()
                                                            ->required()
                                                    ]),
                                                Forms\Components\Grid::make(columns: 2)
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('issueReportImages')
                                                            ->label(label: 'Site Visit Attachments')
                                                            ->enableDownload()
                                                            ->enableOpen()
                                                            ->multiple(),

                                                        Forms\Components\Textarea::make(name: 'issueReport')
                                                            ->label(label: 'Site Visit Report'),
                                                    ])
                                            ]),
                                    ]),

                                Forms\Components\Section::make(heading: 'Completed Reports')
                                    ->schema(components: [
                                        Forms\Components\Repeater::make('completedReports')
                                            ->defaultItems(0)
                                            ->schema([
                                                Forms\Components\Grid::make(columns: 1)
                                                    ->schema([
                                                        Forms\Components\Select::make(name: 'reportUserId')
                                                            ->label(label: 'Contractor')
                                                            ->options(User::query()->whereContractorRole()->pluck('name', 'id')->toArray())
                                                            ->searchable()
                                                            ->preload()
                                                            ->required()
                                                    ]),
                                                Forms\Components\Grid::make(columns: 2)
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('taskCompletedReport')
                                                            ->label(label: 'After Fix Attachments')
                                                            ->enableDownload()
                                                            ->enableOpen()
                                                            ->multiple(),
                                                        // ->visible(fn (Task | null $record) => $record ? $record->isCompleted() : false),

                                                        Forms\Components\Textarea::make(name: 'completedReport')
                                                            ->label(label: 'After Fix Notes')
                                                        // ->visible(fn (Task | null $record) => $record ? $record->isCompleted() : false)
                                                    ]),
                                            ])
                                    ])
                            ])
                        ]),
                ])
                ->submitAction(view('filament.resources.ticket-resource.pages.submit-task-button'))
        ];
    }

    protected function getForms(): array
    {
        return [
            'taskForm' => $this->makeForm()
                ->schema($this->getCreateTaskSchema())
                ->model(Task::class),
        ];
    }

    public function createTask()
    {
        $record = $this->getRecord();
        $data = $this->taskForm->getState();

        $task = Task::query()
            ->create([
                'ticket_id' => $record->id,
                'task_number' => $data['taskNumber'],
                'assignee_id' => $data['assigneeId'],
                'status' => $data['taskStatus'],
                'title' => $data['taskTitle'],
                'description' => $data['taskDescription'],
                'date_time' => $data['dateTime'],
                'due_date' => $data['dueDate']
            ]);

        if ($task) {
            $taskNumber = str_pad(
                string: (string) $task->id,
                length: 6,
                pad_string: '0',
                pad_type: STR_PAD_LEFT
            );

            $task->update([
                'task_number' => $taskNumber
            ]);

            $task->assets()->attach($data['taskAssets']);

            foreach ($data['taskAttachments'] as $taskMedia) {
                $filename = storage_path('app/public/' . $taskMedia);

                $task->addMedia($filename)->toMediaCollection(Task::COLLECTION_NAME, 'public');
            }

            $ticketMedias = $record->getMedia();

            foreach ($ticketMedias as $media) {
                $media->copy($task, Task::COLLECTION_NAME, 'public');
            }

            foreach ($data['costs'] as $cost) {
                $task->costs()->create([
                    'description' => $cost['costDescription'],
                    'cost' => $cost['costValue']
                ]);
            }

            foreach ($data['issueReports'] as $siteVisit) {
                $task->issueReports()->create([
                    'user_id' => $siteVisit['siteVisitUserId'],
                    'issue_report' => $siteVisit['issueReport']
                ]);

                foreach ($siteVisit['issueReportImages'] as $siteVisitMedia) {
                    $filename = storage_path('app/public/' . $siteVisitMedia);
    
                    $task->addMedia($filename)->toMediaCollection(IssueReport::COLLECTION_NAME, 'public');
                }
            }

            foreach ($data['completedReports'] as $completedReport) {
                $task->completedReports()->create([
                    'user_id' => $completedReport['reportUserId'],
                    'notes' => $completedReport['completedReport']
                ]);

                foreach ($completedReport['taskCompletedReport'] as $completedReportMedia) {
                    $filename = storage_path('app/public/' . $completedReportMedia);
    
                    $task->addMedia($filename)->toMediaCollection(TaskCompletedReport::COLLECTION_NAME, 'public');
                }
            }

            activity()
                ->causedBy(resolve(Authenticatable::class)->id)
                ->performedOn($task->ticket)
                ->withProperties([
                    'attributes' => [
                        'task_number' => $taskNumber,
                        'title' => $task->title,
                        'date_time' => $task->date_time
                    ]
                ])
                ->event('task created')
                ->log(description: 'Task with number ' . $taskNumber . ' has been created');

            Notification::make()
                ->title('Success')
                ->body('Task successfully created.')
                ->success()
                ->send();

            return redirect(TicketResource::getUrl('edit', $record) . '?activeRelationManager=2');
        } else {
            Notification::make()
                ->title('Failed')
                ->body('Cannot create Task!. Please try again later.')
                ->danger()
                ->send();

            return;
        }

        // return redirect(TicketResource::)
    }
}
