<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalEntryResource\Pages;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Filament\Forms;
use Filament\Forms\Components\{
    Grid,
    Select,
    DatePicker,
    TextInput,
    Textarea,
    Repeater
};
use Filament\Tables;
use Filament\Tables\Columns\{TextColumn, BadgeColumn};
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

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
                        ->required()
                        ->unique(ignoreRecord: true),

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

                        Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(2)
                            ->nullable(),
                    ])
                    ->columns(4)
                    ->minItems(2)
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')->label('Kode Jurnal')->searchable(),
                TextColumn::make('tanggal')->date()->label('Tanggal'),
                BadgeColumn::make('kategori')->label('Kategori')->colors([
                    'primary' => 'aset',
                    'success' => 'beban_operasional',
                    'warning' => 'penyesuaian',
                    'gray' => 'lainnya',
                ]),
                TextColumn::make('keterangan')->label('Keterangan')->limit(30),
                TextColumn::make('details_count')->counts('details')->label('Jumlah Detail'),
            ])
            ->defaultSort('tanggal', 'desc')
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
}