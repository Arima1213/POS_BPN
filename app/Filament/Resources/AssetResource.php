<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AssetResource\Pages;
use App\Models\Asset;
use App\Models\assets;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssetResource extends Resource
{
    protected static ?string $model = assets::class;
    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('asset_name')->required(),
                Forms\Components\TextInput::make('asset_code')
                    ->required()
                    ->unique(assets::class, 'asset_code')
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

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $journal = self::createJournal($data);

        $data['journal_entry_id'] = $journal->id;

        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $asset = assets::find(request()->route('record'));

        if ($asset && $asset->journal_entry_id) {
            $journal = JournalEntry::find($asset->journal_entry_id);
            if ($journal) {
                $journal->update([
                    'tanggal' => $data['purchase_date'],
                    'keterangan' => "Update Asset: " . $data['asset_name'],
                    'kategori' => 'Asset Update',
                ]);

                foreach ($journal->details as $detail) {
                    $detail->update([
                        'jumlah' => $data['purchase_price'],
                        'deskripsi' => "Update asset " . $data['asset_name'],
                    ]);
                }
            }
        }

        return $data;
    }

    private static function createJournal(array $data)
    {
        $journal = JournalEntry::create([
            'tanggal' => $data['purchase_date'],
            'kode' => 'AUTO-' . now()->format('YmdHis'),
            'keterangan' => 'Pembelian Asset: ' . $data['asset_name'],
            'kategori' => 'Asset',
        ]);

        $assetAccount = ChartOfAccount::where('nama', 'like', '%Aset%')->first();
        $cashAccount = ChartOfAccount::where('nama', 'like', '%Kas%')->first();

        if (!$assetAccount || !$cashAccount) {
            throw new \Exception("Akun Aset atau Kas tidak ditemukan. Mohon periksa Chart of Account.");
        }

        JournalEntryDetail::create([
            'journal_entry_id' => $journal->id,
            'chart_of_account_id' => $assetAccount->id,
            'tipe' => 'debit',
            'jumlah' => $data['purchase_price'],
            'deskripsi' => 'Mencatat aset baru: ' . $data['asset_name'],
        ]);

        JournalEntryDetail::create([
            'journal_entry_id' => $journal->id,
            'chart_of_account_id' => $cashAccount->id,
            'tipe' => 'credit',
            'jumlah' => $data['purchase_price'],
            'deskripsi' => 'Pembayaran aset: ' . $data['asset_name'],
        ]);

        return $journal;
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