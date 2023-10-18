<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use App\Filament\Resources\TicketResource\Actions\SiteVisit\CreateTaskAction;
use Domain\Shared\Ticket\Models\SiteVisit;
use Domain\Shared\Ticket\Models\Ticket;
use Domain\Shared\User\Builders\UserBuilder;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\SiteVisit\Notification\Created\SendNotificationAction;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\Auth\Authenticatable;

class SiteVisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'siteVisits';

    protected static ?string $recordTitleAttribute = 'visit_date';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DateTimePicker::make('visit_date')
                    ->label(label: 'Visit Date')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label(label: 'Contractor')
                    ->relationship(
                        relationshipName: 'user',
                        titleColumnName: 'name',
                        callback: fn (UserBuilder $query) => $query->whereContractorRole()
                    )
                    ->disabled(fn () => Role::customersRole())
                    ->searchable()
                    ->required()
                    ->preload(),
                Forms\Components\Textarea::make('description')
                    ->required(),
                Forms\Components\SpatieMediaLibraryFileUpload::make('site_visit_images')
                    ->enableDownload()
                    ->enableOpen()
                    ->collection(SiteVisit::COLLECTION_NAME)
                    ->multiple(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visit_date')
                    ->label(label: 'Visit Date')
                    ->dateTime()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(label: 'Contractor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->words(6)
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (SiteVisit $record) {
                        $ticketMedias = $record->ticket->getMedia(Ticket::COLLECTION_NAME);

                        foreach ($ticketMedias as $media) {
                            $media->copy($record, SiteVisit::COLLECTION_NAME, 'public');
                        }

                        SendNotificationAction::resolve()
                            ->execute(
                                ticket: $record->ticket,
                                siteVisit: $record
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                CreateTaskAction::make()
                    ->record(fn (SiteVisit $record) => $record),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->hidden(fn () => ! Role::hasAny([Role::admin])),
            ]);
    }
}
