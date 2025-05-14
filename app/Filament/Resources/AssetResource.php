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
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'sold',
                        'warning' => 'damaged',
                        'gray' => 'retired',
                        'secondary' => 'lost',
                    ]),
                Tables\Columns\TextColumn::make('accumulated_depreciation')->money('idr')->label('Akumulasi Penyusutan'),
                Tables\Columns\TextColumn::make('depreciation_start_date')
                    ->date()
                    ->label('Mulai Penyusutan')
                    ->placeholder('Belum ada data'),
                Tables\Columns\TextColumn::make('depreciation_method')
                    ->label('Metode Penyusutan')
                    ->formatStateUsing(fn($state) => $state === 'straight_line' ? 'Garis Lurus' : 'Saldo Menurun'),
                Tables\Columns\TextColumn::make('is_fully_depreciated')
                    ->label('Depriasi Penuh')
                    ->formatStateUsing(fn($state) => $state == '1' ? 'Sudah' : 'Belum')
                    ->colors([
                        'success' => fn($state) => $state == '1',
                        'danger' => fn($state) => $state == '0',
                    ])
                    ->badge(),
                Tables\Columns\TextColumn::make('book_value')->money('idr')->label('Nilai Buku'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('dispose')
                    ->label('Lepas Aset')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->visible(fn($record) => $record->status === 'active')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $asset = $record;

                        $bookValue = $asset->purchase_price - $asset->accumulated_depreciation;
                        $sellPrice = 0;
                        $difference = $sellPrice - $bookValue;

                        DB::transaction(function () use ($asset, $bookValue, $difference) {
                            $journal = JournalEntry::create([
                                'tanggal' => now(),
                                'kode' => 'DIS-' . strtoupper(uniqid()),
                                'keterangan' => 'Pelepasan Aset: ' . $asset->asset_name,
                                'kategori' => 'aset',
                            ]);

                            $accumulatedAccount = ChartOfAccount::where('kode', '1990')->first(); // Akumulasi Penyusutan
                            $assetAccount = ChartOfAccount::where('kode', '1100')->first(); // Aset Tetap
                            $gainLossAccount = ChartOfAccount::where('kode', '4030')->first(); // Laba/Rugi Pelepasan Aset

                            // Kredit aset
                            JournalEntryDetail::create([
                                'journal_entry_id' => $journal->id,
                                'chart_of_account_id' => $assetAccount->id,
                                'tipe' => 'kredit',
                                'jumlah' => $asset->purchase_price,
                                'deskripsi' => 'Penghapusan aset: ' . $asset->asset_name,
                            ]);

                            // Debit akumulasi penyusutan
                            JournalEntryDetail::create([
                                'journal_entry_id' => $journal->id,
                                'chart_of_account_id' => $accumulatedAccount->id,
                                'tipe' => 'debit',
                                'jumlah' => $asset->accumulated_depreciation,
                                'deskripsi' => 'Penghapusan akumulasi penyusutan',
                            ]);

                            // Laba/Rugi selisih
                            JournalEntryDetail::create([
                                'journal_entry_id' => $journal->id,
                                'chart_of_account_id' => $gainLossAccount->id,
                                'tipe' => $difference >= 0 ? 'kredit' : 'debit',
                                'jumlah' => abs($difference),
                                'deskripsi' => 'Selisih nilai buku vs jual',
                            ]);

                            $asset->update([
                                'status' => 'retired',
                            ]);
                        });
                    }),
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