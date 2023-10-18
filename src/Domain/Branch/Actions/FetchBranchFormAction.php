<?php

namespace Domain\Branch\Actions;

use Domain\Shared\User\Models\Branch;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use KoalaFacade\DiamondConsole\Foundation\Action;

readonly class FetchBranchFormAction extends Action
{
    public function execute(): array
    {
        return [
            Grid::make(columns: 2)
                ->schema([
                    TextInput::make('name')
                        ->label(label: 'Outlet Name')
                        ->required()
                        ->maxLength(255),
                    SpatieMediaLibraryFileUpload::make('branch_images')
                        ->enableDownload()
                        ->enableOpen()
                        ->collection(Branch::COLLECTION_NAME)
                        ->multiple(),
                    Textarea::make('description'),
                    Textarea::make('address')
                        ->required(),
                    TextInput::make('person_in_charge')
                        ->label(label: 'Outlet In Charge Person')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->label(label: 'Outlet Phone Number')
                        ->numeric()
                        ->required()
                        ->mask(fn (TextInput\Mask $mask) => $mask
                            ->numeric()
                            ->pattern('000000000000000')
                        ),
                    TextInput::make('latitude')
                        ->numeric()
                        ->mask(fn (TextInput\Mask $mask) => $mask
                            ->numeric()
                            ->decimalPlaces(10)
                        ),
                    TextInput::make('longitude')
                        ->numeric()
                        ->mask(fn (TextInput\Mask $mask) => $mask
                            ->numeric()
                            ->decimalPlaces(10)
                        ),
                    
                ])
        ];
    }
} 