<?php

namespace App\Filament\Resources\TicketResource\RelationManagers;

use Carbon\Carbon;
use Domain\Shared\Ticket\Models\Message;
use Domain\Ticket\Actions\Message\AfterUpsertMessageAction;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages';

    protected static ?string $recordTitleAttribute = 'subject';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make(name: 'sender_id')
                    ->label(label: 'Sender')
                    ->relationship(
                        relationshipName: 'sender',
                        titleColumnName: 'name'
                    )
                    ->default(state: resolve(Authenticatable::class)->id)
                    ->disabled(condition: true),

                Section::make(heading: 'Message')
                    ->schema([
                        Group::make([
                            TextInput::make(name: 'subject')
                                ->required()
                                ->maxLength(length: 255),

                            MarkdownEditor::make(name: 'body')
                                ->required()
                                ->disableToolbarButtons([
                                    'codeBlock',
                                ]),
                        ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sender.email'),
                Tables\Columns\TextColumn::make('subject'),
                Tables\Columns\TextColumn::make('body')
                    ->words(6),
                Tables\Columns\TextColumn::make('created_at')
                    ->getStateUsing(fn (Message $record) => Carbon::parse($record->created_at)->diffForHumans()),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->using(function (Tables\Contracts\HasRelationshipTable $livewire, array $data): Model {
                        $created = $livewire->getRelationship()->create($data);

                        AfterUpsertMessageAction::resolve()->execute($created);

                        return $created;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                //                Tables\Actions\EditAction::make(),
                //                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }
}
