<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Infrastructure\Filament\RedirectToIndex;

class CreateService extends CreateRecord
{
    use RedirectToIndex;

    protected static string $resource = ServiceResource::class;
}
