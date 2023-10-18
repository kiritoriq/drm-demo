<?php

namespace App\Filament\Resources\TicketResource\Actions;

use App\Filament\Resources\TicketResource\Pages\ViewLegend;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\Action;

class LegendAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->button();

        $this->label(label: 'Color Legend');

        $this->icon(icon: 'heroicon-o-eye');

        $this->color(color: 'secondary');

        $this->modalWidth(width: 'md');

        $this->modalActions(fn (): array => array_merge(
            $this->getExtraModalActions(),
            [$this->getModalCancelAction()->label(__('filament-support::actions/view.single.modal.actions.close.label'))],
        ));

        $this->modalContent(view('filament.resources.ticket-resource.pages.view-legend'));

        $this->action(fn () => $this->record);
    }

    public static function getDefaultName(): ?string
    {
        return 'show-legend';
    }
}