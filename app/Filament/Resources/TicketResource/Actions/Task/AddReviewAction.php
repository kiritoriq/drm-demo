<?php

namespace App\Filament\Resources\TicketResource\Actions\Task;

use Domain\Shared\Ticket\Models\Task;
use Domain\Ticket\Enums\Task\JobStatus;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;
use Yepsua\Filament\Forms\Components\Rating;

class AddReviewAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(label: 'Add Review');

        $this->icon(icon: 'heroicon-o-star');

        $this->color(color: 'primary');

        $this->visible(fn (Task $record): bool => $record->eligibleToReview());

        $this->mountUsing(fn (ComponentContainer $form, Task $record) => $form->fill([
            'taskId' => $record->id,
            'taskNumber' => $record->task_number,
            'assigneeName' => $record->assignee?->name,
            'customerName' => $record->ticket->customer?->name,
            'status' => $record->status,
            'completedAt' => $record->completed_at
        ]));

        $this->form(function (Task $record) {
            return [
                Grid::make(3)
                    ->schema([
                        TextInput::make('taskNumber')
                            ->label('Task Number')
                            ->disabled(),
                        TextInput::make('assigneeName')
                            ->label('Assignee (Contractor)')
                            ->disabled(),
                        TextInput::make('customerName')
                            ->label('Customer')
                            ->disabled(),
                    ]),
                Grid::make(2)
                    ->schema([
                        Select::make('status')
                            ->options(JobStatus::getCaseOptions())
                            ->disabled(),
                        DateTimePicker::make('completedAt')
                            ->disabled()
                    ]),
                Grid::make(2)
                    ->schema([
                        Textarea::make('text_review')
                            ->required(),
                        Rating::make('rating')
                            ->min(1)
                            ->max(5)
                            ->size(5)
                            ->clearable()
                            ->clearIconColor('red')
                            ->clearIconTooltip('Clear')
                    ])
            ];
        });

        $this->action(function (Task $record, array $data) {
            $record->review()->create([
                'customer_id' => $record->ticket->customer_id,
                'contractor_id' => $record->assignee_id,
                'text_review' => $data['text_review'],
                'stars' => $data['rating']
            ]);
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'add-review';
    }
}