<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Domain\Shared\User\Enums\Role;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PreventiveServicesRelationManager extends RelationManager
{
    protected static string $relationship = 'preventiveServices';

    protected static ?string $recordTitleAttribute = 'title';

    public static function canViewForRecord(Model $ownerRecord): bool
    {
        return ! Role::exactlyCustomerRole();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset.asset_code')
                    ->label(label: 'Asset Code'),
                Tables\Columns\TextColumn::make('asset.name')
                    ->label(label: 'Asset Name'),
                Tables\Columns\TextColumn::make('next_service_date')
                    ->label(label: 'Next Service Date')
                    ->date()
            ])
            ->filters([
                //
            ]);
    }    
}
