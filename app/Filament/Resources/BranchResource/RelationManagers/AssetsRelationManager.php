<?php

namespace App\Filament\Resources\BranchResource\RelationManagers;

use App\Filament\Resources\BranchResource\Actions\Asset\AssetHistoriesAction;
use App\Filament\Resources\BranchResource\Actions\Asset\ViewQRCodeAction;
use Domain\Shared\User\Builders\UserBuilder;
use Domain\Shared\User\Models\BranchAsset;
use Domain\Shared\User\ValueObjects\Branch\Asset\AssetCode;
use Filament\Forms;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class AssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(1)
                    ->schema([
                        Forms\Components\TextInput::make('asset_code')
                            ->label(label: 'Asset Code')
                            ->default(function (RelationManager $livewire, callable $get) {
                                $assetCode = new AssetCode(
                                    latestAssetCode: BranchAsset::query()->where('branch_id', $livewire->ownerRecord->id)->count() + 1,
                                    branchId: $livewire->ownerRecord->id,
                                    categoryId: $get('asset_type')
                                );

                                return $assetCode->formatted;
                            })
                            ->disabled()
                            ->required(),
                    ]),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('brand')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('model')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year_make')
                    ->label(label: 'Year Make')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('description'),

                Forms\Components\Section::make('Asset Location')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label(label: 'Company Name')
                            ->dehydrated(false)
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->owner->company_name)
                            ->disabled(),
                        Forms\Components\TextInput::make('outlet_loc')
                            ->label(label: 'Outlet Location')
                            ->dehydrated(false)
                            ->default(fn (RelationManager $livewire) => $livewire->ownerRecord->name)
                            ->disabled(),
                        Forms\Components\Select::make('area_id')
                            ->relationship(
                                relationshipName: 'area',
                                titleColumnName: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('asset_type')
                            ->relationship(
                                relationshipName: 'assetType',
                                titleColumnName: 'name',
                                callback: fn (Builder $query) => $query->orderBy('id', 'asc')
                            )
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->required()
                            ->afterStateUpdated(function (callable $get, callable $set, RelationManager $livewire, BranchAsset | null $record) {
                                $assetCode = new AssetCode(
                                    latestAssetCode: BranchAsset::query()->where('branch_id', $livewire->ownerRecord->id)->count() + 1,
                                    branchId: $livewire->ownerRecord->id,
                                    categoryId: $get('asset_type')
                                );

                                return $set('asset_code', $assetCode->formatted);
                            })
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->maxLength(length: 255)
                                    ->required(),
                            ]),
                    ]),

                Forms\Components\Section::make('More Info')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('vendor_purchased_from')
                            ->label(label: 'Purchased From (Vendor)')
                            ->maxLength(length: 255)
                            ->required(),
                        Forms\Components\DatePicker::make('warranty_expiry_date')
                            ->label(label: 'Warranty Expiry Date'),
                        Forms\Components\TextInput::make('year_purchased')
                            ->label(label: 'Year Purchased')
                            ->numeric()
                            ->required(),
                        Forms\Components\SpatieMediaLibraryFileUpload::make(BranchAsset::IMAGE_COLLECTION_NAME)
                            ->collection(BranchAsset::IMAGE_COLLECTION_NAME)
                            ->enableDownload()
                            ->enableOpen()
                            ->acceptedFileTypes(['image/*'])
                            ->multiple(),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('Warranty Files')
                            ->collection(BranchAsset::WARRANTY_FILE_COLLECTION_NAME)
                            ->acceptedFileTypes(['application/pdf'])
                            ->enableDownload()
                            ->enableOpen()
                            ->multiple(),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('Files Upload')
                            ->collection(BranchAsset::FILE_COLLECTION_NAME)
                            ->acceptedFileTypes(['application/pdf'])
                            ->enableDownload()
                            ->enableOpen()
                            ->multiple(),
                    ]),
                
                Forms\Components\Section::make('Preventive Services')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('preventive_cycle')
                            ->options([
                                'month' => 'Month',
                                'year' => 'Year'
                            ]),
                        Forms\Components\TextInput::make('preventive_service')
                            ->numeric()
                            ->mask(fn (Mask $mask) => $mask
                                ->numeric()
                            ),
                    ]),

                Forms\Components\Section::make('Parts')
                    ->schema([
                        Forms\Components\Repeater::make('parts')
                            ->relationship()
                            ->grid(columns: 3)
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->maxLength(length: 255)
                                    ->required(),
                                Forms\Components\Textarea::make('description'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_code')
                    ->label(label: 'Code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ViewQRCodeAction::make()
                    ->record(fn (BranchAsset $record) => $record),
                AssetHistoriesAction::make()
                    ->record(fn (BranchAsset $record) => $record),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
