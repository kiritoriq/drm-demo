<?php

namespace App\Filament\Resources\AreaResource\Pages;

use App\Filament\Resources\AreaResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Infrastructure\Filament\RedirectToIndex;

class EditArea extends EditRecord
{
    use RedirectToIndex;

    protected static string $resource = AreaResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
