<?php

namespace App\Filament\Resources\ContractorResource\Pages;

use App\Filament\Resources\ContractorResource;
use App\Filament\Resources\ContractorResource\Widgets\CalendarWidget;
use Filament\Resources\Pages\Page;

class ViewCalendar extends Page
{
    protected static string $resource = ContractorResource::class;

    protected static string $view = 'filament.resources.contractor-resource.widgets.calendar-widget';

    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class
        ];
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}