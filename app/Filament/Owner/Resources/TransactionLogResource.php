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

class TransactionLogResource extends Resource
{
    protected static ?string $model = Transactions::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->query(function () {
                return Transactions::query()
                    ->selectRaw('MIN(id) as id, DATE(created_at) as transaction_date, SUM(total) as total_sales, SUM(paid_amount) as total_paid')
                    ->groupBy('transaction_date')
                    ->orderBy('transaction_date', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date()
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
                // Tidak perlu bulk delete
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactionLogs::route('/'),
        ];
    }
}