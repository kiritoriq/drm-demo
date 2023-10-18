<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Domain\Shared\User\Enums\Role;
use Domain\Shared\User\Models\User;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Activitylog\Models\Activity;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $recordTitleAttribute = 'description';

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
                Tables\Columns\TextColumn::make('log_name'),
                Tables\Columns\TextColumn::make('event'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('properties')
                    ->getStateUsing(function (Activity $record) {
                        if (strpos($record->event, 'created') === false && strpos($record->event, 'deleted') === false) {
                            $prop = [];
                            foreach ($record->properties['attributes'] as $key => $attr) {
                                if ($key !== 'updated_at') {
                                    $prop[] = $key . ': ' . $attr;
                                }
                            }

                            return implode(',', $prop);
                        }

                        return '*';
                    }),
                Tables\Columns\TextColumn::make('causer_id')
                    ->label(label: 'Performed By')
                    ->getStateUsing(fn (Activity $record) => User::find($record->causer_id)->name),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(label: 'Performed At')
                    ->dateTime()
            ])
            ->filters([
                //
            ]);
    }    
}
