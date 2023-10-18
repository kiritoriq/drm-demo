<?php

namespace App\Filament\Resources\TicketResource\Pages;

use App\Filament\Resources\TicketResource;
use Exception;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    public array $assets = [];

    /**
     * @return array<int, Actions\Action>
     *
     * @throws Exception
     */
    protected function getActions(): array
    {
        return [
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['ticket_number'] = explode('-', $data['ticket_number'])[1];

        return $data;
    }
}
