<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Asset Perusahaan';
    protected static ?string $navigationGroup = 'Inventaris';
    protected static ?string $pluralLabel = 'Kelola Aset';
    protected static ?string $slug = 'kelola-aset';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('asset_name')->required(),
                Forms\Components\TextInput::make('asset_code')
                    ->required()
                    ->unique(Asset::class, 'asset_code')
                    ->default(fn() => 'ASSET-' . strtoupper(uniqid())),
                Forms\Components\Textarea::make('description'),
                Forms\Components\TextInput::make('purchase_price')->required()->numeric(),
                Forms\Components\DatePicker::make('purchase_date')->required(),
                Forms\Components\TextInput::make('useful_life_years')->required()->numeric(),
                Forms\Components\TextInput::make('residual_value')->numeric()->default(0),
                Forms\Components\TextInput::make('location'),
                Forms\Components\Select::make('category')
                    ->options([
                        'vehicle' => 'Kendaraan',
                        'office_equipment' => 'Peralatan Kantor',
                        'building' => 'Bangunan',
                        'land' => 'Tanah',
                        'others' => 'Lainnya',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'Aktif',
                        'sold' => 'Terjual',
                        'damaged' => 'Rusak',
                        'lost' => 'Hilang',
                        'retired' => 'Pensiun',
                    ])
                    ->default('active'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_name')->searchable(),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('purchase_price')->money('idr'),
                Tables\Columns\TextColumn::make('purchase_date')->date(),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}