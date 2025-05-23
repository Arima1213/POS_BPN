<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Exports\TransactionLogOwnerExporter;
use App\Filament\Owner\Resources\TransactionLogResource\Pages;
use App\Models\Transactions;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class TransactionLogResource extends Resource
{
    protected static ?string $model = Transactions::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Log Transaksi';
    protected static ?string $pluralLabel = 'Log Transaksi';
    protected static ?string $slug = 'log-transaksi';
    protected static ?string $navigationLabel = 'Log Transaksi';
    protected static ?string $navigationGroup = 'Laporan Transaksi';

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Transactions::query()
                    ->select([
                        DB::raw('MIN(id) as id'),
                        DB::raw('DATE(created_at) as transaction_date'),
                        DB::raw('COUNT(*) as transaction_count'), // Tambahan untuk hitung jumlah transaksi
                        DB::raw('SUM(total) as total_sales'),
                        DB::raw('SUM(paid_amount) as total_paid')
                    ])
                    ->groupBy(DB::raw('DATE(created_at)'))
                    ->orderByDesc(DB::raw('DATE(created_at)'));
            })
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_count')
                    ->label('Jumlah Transaksi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_sales')
                    ->label('Total Penjualan')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_paid')
                    ->label('Total Dibayar')
                    ->money('idr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('outstanding')
                    ->label('Total Belum Dibayar')
                    ->money('idr')
                    ->state(function ($record) {
                        return $record->total_sales - $record->total_paid;
                    })
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->whereDate('created_at', '>=', $data['from']))
                            ->when($data['until'], fn($q) => $q->whereDate('created_at', '<=', $data['until']));
                    })
                    ->label('Filter Tanggal'),
            ])
            ->actions([])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\ExportBulkAction::make()
                //         ->exporter(TransactionLogOwnerExporter::class)
                // ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionLogs::route('/'),
        ];
    }
}
