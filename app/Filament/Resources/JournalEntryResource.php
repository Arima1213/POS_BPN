<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    DatePicker,
    Grid,
    Select,
    Textarea,
    Repeater,
    TextInput,
    FileUpload
};
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};

use Filament\Resources\Resource;

class JournalEntryResource extends Resource
{
    protected static ?string $model = JournalEntry::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Pencatatan Jurnal';
    protected static ?string $navigationGroup = 'Akuntansi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    TextInput::make('kode')
                        ->label('Kode Jurnal')
                        ->default(fn() => self::generateKodeJurnal())
                        ->disabled()
                        ->dehydrated()
                        ->required(),

                    DatePicker::make('tanggal')
                        ->label('Tanggal')
                        ->default(now())
                        ->required(),
                ]),

                Select::make('kategori')
                    ->label('Kategori Jurnal')
                    ->options([
                        'aset' => 'Aset',
                        'beban_operasional' => 'Beban Operasional',
                        'penyesuaian' => 'Penyesuaian',
                        'lainnya' => 'Lainnya',
                    ])
                    ->required(),

                Textarea::make('keterangan')
                    ->label('Keterangan')
                    ->rows(2)
                    ->nullable(),

                Repeater::make('details')
                    ->label('Rincian Akun')
                    ->relationship('details')
                    ->schema([
                        Select::make('chart_of_account_id')
                            ->label('Akun')
                            ->options(ChartOfAccount::all()->pluck('nama', 'id'))
                            ->searchable()
                            ->required(),

                        Select::make('tipe')
                            ->label('Tipe')
                            ->options([
                                'debit' => 'Debit',
                                'kredit' => 'Kredit',
                            ])
                            ->required(),

                        TextInput::make('jumlah')
                            ->label('Jumlah (Rp)')
                            ->numeric()
                            ->required(),

                        FileUpload::make('lampiran')
                            ->label('Lampiran')
                            ->directory('lampiran-jurnal')
                            ->maxSize(1024)
                            ->nullable(),

                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->columns(5)
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')->label('Kode Jurnal')->searchable()->sortable(),
                TextColumn::make('tanggal')->date()->label('Tanggal')->sortable(),
                BadgeColumn::make('kategori')->label('Kategori')->colors([
                    'primary' => 'aset',
                    'success' => 'beban_operasional',
                    'warning' => 'penyesuaian',
                    'gray' => 'lainnya',
                ])->sortable(),
                TextColumn::make('keterangan')->label('Keterangan')->limit(30),
                TextColumn::make('details_count')->counts('details')->label('Jumlah Detail'),
            ])
            ->defaultSort('tanggal', 'desc')
            ->defaultSort('created_at', 'desc')
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalEntries::route('/'),
            'create' => Pages\CreateJournalEntry::route('/create'),
            'edit' => Pages\EditJournalEntry::route('/{record}/edit'),
        ];
    }

    private static function generateKodeJurnal(): string
    {
        $tanggal = now()->format('Ymd');
        $countToday = JournalEntry::whereDate('created_at', now())->count() + 1;
        return 'JU-' . $tanggal . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);
    }
}
