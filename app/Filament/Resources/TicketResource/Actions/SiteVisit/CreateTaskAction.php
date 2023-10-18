<?php

namespace App\Filament\Resources\TicketResource\Actions\SiteVisit;

use Domain\Shared\Ticket\Models\SiteVisit;
use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\BranchAsset;
use Domain\Shared\User\Models\User;
use Domain\Ticket\Enums\Task\JobStatus;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CreateTaskAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(label: 'Create Task');

        $this->icon(icon: 'heroicon-o-plus-circle');

        $this->color(color: 'secondary');

        $this->visible(! Role::customersRole());

        $this->mountUsing(fn (ComponentContainer $form, SiteVisit $record) => $form->fill([
            'contractor_id' => $record->user_id,
            'description' => $record->description,
            'date_time' => $record->visit_date,
            'title' => 'Site Visit Job',
            'task_number' => str_pad(Task::count() + 1, 6, '0', STR_PAD_LEFT),
            'status' => JobStatus::New->value
        ]));

        $this->form(fn (SiteVisit $record) => [
            Grid::make(3)
                ->schema([
                    TextInput::make('task_number')
                        ->disabled(),
                    Select::make(name: 'contractor_id')
                        ->label(label: 'Contractor')
                        ->options(User::query()->whereRelation('roles', fn (Builder $query) => $query->where('name', 'Contractor'))->pluck('name', 'id'))
                        ->disabled(),
                    Select::make(name: 'status')
                        ->options(JobStatus::getCaseOptions())
                        ->searchable()
                        ->preload(),
                ]),
            
            Grid::make(2)
                ->schema([
                    Group::make([
                        TextInput::make('title')
                            ->required()
                            ->default('Site Visit Job')
                            ->maxLength(255),
    
                        Textarea::make('description'),
                    ]),
    
                    Group::make([
                        DateTimePicker::make('date_time')
                            ->label(label: 'Start Date and Time')
                            ->required(),
                        
                        DatePicker::make('due_date')
                            ->label(label: 'Due Date')
                            ->required(),
    
                        Select::make('assets')
                            ->label(label: 'Assets')
                            ->options(
                                BranchAsset::query()
                                    ->where('branch_id', $record->ticket->branch_id)
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->multiple(),
                        
                        SpatieMediaLibraryFileUpload::make('task_images')
                            ->collection(Task::COLLECTION_NAME)
                            ->label(label: 'Task Attachments')
                    ]),
                ]),

            Grid::make(1)
                ->schema([
                    SpatieMediaLibraryFileUpload::make('site_visit_images')
                        ->collection(SiteVisit::COLLECTION_NAME)
                        ->label(label: 'Site Visit Attachments')
                        ->multiple()
                        ->dehydrated(false)
                        ->disabled()
                ])
        ]);

        $this->action(function (SiteVisit $record, array $data) {
            $task = Task::create([
                'ticket_id' => $record->ticket_id,
                'task_number' => $data['task_number'],
                'assignee_id' => $data['contractor_id'],
                'status' => $data['status'],
                'title' => $data['title'],
                'description' => $data['description'],
                'date_time' => $data['date_time'],
                'due_date' => $data['due_date']
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
    
                $task->assets()->attach($data['assets']);

                $recordMedias = $record->getMedia(SiteVisit::COLLECTION_NAME);

                foreach ($recordMedias as $media) {
                    $media->copy($task, Task::COLLECTION_NAME, 'public');
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

                return;
            } else {
                Notification::make()
                    ->title('Failed')
                    ->body('Cannot create Task!. Please try again later.')
                    ->danger()
                    ->send();
                
                return;
            }
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'create-task';
    }
}