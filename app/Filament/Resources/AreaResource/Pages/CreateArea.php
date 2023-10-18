<?php

namespace App\Filament\Resources\AreaResource\Pages;

use App\Filament\Resources\AreaResource;
use Filament\Resources\Pages\CreateRecord;
use Infrastructure\Filament\RedirectToIndex;

class CreateArea extends CreateRecord
{
    use RedirectToIndex;

    protected static string $resource = AreaResource::class;
}
