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
use Filament\Tables\Actions\ViewAction;
use Yepsua\Filament\Forms\Components\Rating;

class ViewReviewAction extends ViewAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(label: 'View Review');

        $this->icon(icon: 'heroicon-o-eye');

        $this->color(color: 'secondary');

        $this->visible(fn (Task $record): bool => $record->review()->exists());

        $this->mountUsing(fn (ComponentContainer $form, Task $record) => $form->fill([
            'taskId' => $record->id,
            'taskNumber' => $record->task_number,
            'assigneeName' => $record->assignee?->name,
            'customerName' => $record->ticket->customer?->name,
            'status' => $record->status,
            'completedAt' => $record->completed_at,
            'textReview' => $record->review->text_review,
            'stars' => $record->review->stars
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
                        Textarea::make('textReview')
                            ->required()
                            ->disabled(),
                        Rating::make('stars')
                            ->min(1)
                            ->max(5)
                            ->size(5)
                    ])
            ];
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'view-review';
    }
}