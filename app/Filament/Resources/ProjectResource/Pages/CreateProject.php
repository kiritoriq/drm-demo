<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;
use Infrastructure\Filament\RedirectToIndex;

class CreateProject extends CreateRecord
{
    use RedirectToIndex;

    protected static string $resource = ProjectResource::class;
}
