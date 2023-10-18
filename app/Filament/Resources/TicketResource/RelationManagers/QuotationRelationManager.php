<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use App\Filament\Resources\TicketResource\Actions\Quotation\AcceptAction;
use App\Filament\Resources\TicketResource\Actions\Quotation\RejectAction;
use Domain\Shared\Ticket\Models\Quotation;
use Domain\Shared\User\Enums\Role;
use Domain\Ticket\Actions\Quotation\Notification\Created\SendNotificationAction;
use Domain\Ticket\Enums\Status;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

class QuotationRelationManager extends RelationManager
{
    protected static string $relationship = 'quotations';

    protected static ?string $recordTitleAttribute = 'ticket.ticket_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('quotation_number')
                    ->label(label: 'Quotation Number')
                    ->required(),
                Forms\Components\Select::make('raised_by_id')
                    ->relationship('raisedBy', 'name')
                    ->default(resolve(Authenticatable::class)->id)
                    ->disabled(fn () => Role::doesntHave(Role::admin)),
                Forms\Components\DateTimePicker::make('quotation_date')
                    ->required()
                    ->default(now()),
                Forms\Components\TextInput::make('total_amount')
                    ->label(label: 'Total Amount')
                    ->prefix('RM')
                    ->numeric()
                    ->mask(fn (Forms\Components\TextInput\Mask $mask) => $mask
                        ->numeric()
                        ->decimalPlaces(places: 2)
                    ),
                Forms\Components\Textarea::make('remarks'),
                Forms\Components\SpatieMediaLibraryFileUpload::make('quotation_letters')
                    ->collection(Quotation::COLLECTION_NAME)
                    ->acceptedFileTypes(['application/pdf'])
                    ->enableDownload()
                    ->enableOpen(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('quotation_number')
                    ->label(label: 'Quotation No.'),
                Tables\Columns\TextColumn::make('quotation_date')
                    ->label(label: 'Quotation Date')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('raisedBy.name')
                    ->label(label: 'Raised By')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->label(label: 'Total Amount')
                    ->money(currency: 'myr', shouldConvert: true),
                Tables\Columns\TextColumn::make('remarks')
                    ->words(6),
                Tables\Columns\IconColumn::make('is_client_agreed')
                    ->label(label: 'Client Agreed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(label: 'Created Date')
                    ->dateTime(),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->disableCreateAnother(true)
                    ->after(function (Quotation $record) {
                        if (! $record->ticket->isQuotationUploaded()) {
                            $record->ticket->update([
                               'status' => Status::QuoteRequested->value
                            ]);
                        }

                        SendNotificationAction::resolve()
                            ->execute(
                                ticket: $record->ticket,
                                quotation: $record
                            );

                        return activity()
                                ->causedBy(resolve(Authenticatable::class)->id)
                                ->performedOn($record->ticket)
                                ->withProperties([
                                    'attributes' => [
                                        'quotation_number' => $record->quotation_number,
                                        'quotation_date' => $record->quotation_date
                                    ]
                                ])
                                ->event('quotation created')
                                ->log(description: 'Quotation has been created');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn (Quotation $record) => $record->ticket->agreedQuotation() <= 0),
                AcceptAction::make(),
                RejectAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
