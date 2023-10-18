<?php

namespace App\Filament\Resources\VersionResource\Pages;

use App\Filament\Resources\VersionResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Infrastructure\Filament\RedirectToIndex;

class CreateVersion extends CreateRecord
{
    use RedirectToIndex;
    
    protected static string $resource = VersionResource::class;
}
