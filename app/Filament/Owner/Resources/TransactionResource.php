<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\TransactionResource\Pages;
use App\Models\OwnerTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    protected static ?string $model = OwnerTransaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';
    protected static ?string $label = 'Transaksi Pemilik';
    protected static ?string $pluralLabel = 'Transaksi Pemilik';
    protected static ?string $slug = 'transaksi-pemilik';
    protected static ?string $navigationLabel = 'Transaksi Pemilik';
    protected static ?string $navigationGroup = 'Inventaris';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('tanggal')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('tipe')
                    ->required()
                    ->options([
                        'setor_modal' => 'Setor Modal',
                        'prive' => 'Prive',
                    ]),
                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->label('Jumlah (Rp)'),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipe')
                    ->label('Jenis Transaksi')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('jumlah')
                    ->label('Jumlah (Rp)')
                    ->money('IDR')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(20)
                    ->tooltip(fn($record) => $record->keterangan),
            ])
            ->defaultSort('tanggal', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}