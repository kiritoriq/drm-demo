<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Domain\Shared\Foundation\Support\Str;
use Domain\Shared\Ticket\Models\TicketReport;
use Domain\Shared\User\Enums\Role;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportsRelationManager extends RelationManager
{
    protected static string $relationship = 'reports';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(name: 'created_by')
                    ->label(label: 'Created By')
                    ->required()
                    ->relationship(
                        relationshipName: 'createdBy',
                        titleColumnName: 'name'
                    )
                    ->default(resolve(Authenticatable::class)->id)
                    ->searchable()
                    ->preload()
                    ->disabled(fn (TicketReport | null $record) => Role::doesntHave(Role::admin) || $record),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description'),
                SpatieMediaLibraryFileUpload::make('attachments')
                    ->collection(TicketReport::COLLECTION_NAME)
                    ->multiple()
                    ->enableDownload()
                    ->enableOpen()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->description(description: fn (TicketReport $record): string => filled ($record->description) ? Str::descriptionText($record->description) : '', position: 'below')
                    ->searchable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(label: 'Created By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(label: 'Created At')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (TicketReport $record) => $record->is_generated !== 1),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (TicketReport $record) => $record->is_generated !== 1),
            ]);
    }    
}
