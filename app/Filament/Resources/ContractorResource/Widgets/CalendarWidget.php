<?php

namespace App\Filament\Resources\ContractorResource\Widgets;

use Domain\Shared\Ticket\Models\Task;
use Domain\Shared\User\Models\User;
use Domain\Task\Actions\FetchTaskCalendarAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{

    public string | null $contractor_id = null;

    public function getQueryString()
    {
        return parent::getQueryString() + ['contractor_id' => ['except' => '']];
    }

    public static function canCreate(): bool
    {
        // Returning 'false' will remove the 'Create' button on the calendar.
        return false;
    }

    public static function canEdit(?array $event = null): bool
    {
        return false;
    }

    /**
     * Return events that should be rendered statically on calendar.
     */
    public function getViewData(): array
    {
        if (filled ($this->contractor_id)) {
            return FetchTaskCalendarAction::resolve()
                ->execute(
                    user: User::find(decrypt($this->contractor_id))
                );
        }

        return FetchTaskCalendarAction::resolve()
            ->execute(null);
    }

    /**
     * FullCalendar will call this function whenever it needs new event data.
     * This is triggered when the user clicks prev/next or switches views on the calendar.
     */
    public function fetchEvents(array $fetchInfo): array
    {
        // You can use $fetchInfo to filter events by date.
        return [];
    }

    protected static function getEditEventFormSchema(): array
    {
        return [
            Textarea::make('title')
                ->disabled(),
            DatePicker::make('start')
                ->required()
                ->disabled(),
        ];
    }

    // Resolve Event record into Model property
    public function resolveEventRecord(array $data): Model
    {
        // Using Appointment class as example
        return Task::find($data['id']);
    }

    public function getEditEventModalTitle(): string
    {
        return 'View Task';
    }

    public function getEditEventModalSubmitButtonLabel(): string
    {
        return __('filament-support::actions/view.single.modal.actions.close.label');
    }

    public function getEditEventModalCloseButtonLabel(): string
    {
        return $this->editEventForm->isDisabled()
            ? __('filament-support::actions/view.single.modal.actions.close.label')
            : __('filament::resources/pages/edit-record.form.actions.cancel.label');
    }
}
