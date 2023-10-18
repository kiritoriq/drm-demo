<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Resources\Pages\CreateRecord;
use Infrastructure\Filament\RedirectToIndex;

class CreateAsset extends CreateRecord
{
    use RedirectToIndex;

    protected static string $resource = AssetResource::class;
}
