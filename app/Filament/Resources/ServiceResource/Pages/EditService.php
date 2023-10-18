<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Infrastructure\Filament\RedirectToIndex;

class EditService extends EditRecord
{
    use RedirectToIndex;
    
    protected static string $resource = ServiceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
