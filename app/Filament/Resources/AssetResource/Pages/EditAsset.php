<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Infrastructure\Filament\RedirectToIndex;

class EditAsset extends EditRecord
{
    use RedirectToIndex;

    protected static string $resource = AssetResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
