<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionLogResource\Pages;
use App\Models\Transactions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\TransactionLogExporter;
use Filament\Tables\Actions\ExportBulkAction;

class TransactionLogResource extends Resource
{
    protected static ?string $model = Transactions::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Riwayat Transaksi';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $pluralLabel = 'Riwayat Transaksi';
    protected static ?string $slug = 'riwayat-transaksi';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),

                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR', true),

                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Dibayar')
                    ->money('IDR', true),

                Tables\Columns\TextColumn::make('change_amount')
                    ->label('Kembalian')
                    ->money('IDR', true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));
                    }),
            ])
            // ->headerActions([
            //     ExportAction::make()
            //         ->exporter(TransactionLogExporter::class)
            //         ->formats([
            //             ExportFormat::Xlsx,
            //             ExportFormat::Csv,
            //         ])
            // ])
            ->actions([])
            ->bulkActions([
                ExportBulkAction::make()
                    ->exporter(TransactionLogExporter::class)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionLogs::route('/'),
        ];
    }
}