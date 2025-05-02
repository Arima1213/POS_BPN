<?php

namespace App\Filament\Resources\ProductStocksResource\Widgets;

use App\Models\Stock;
use App\Models\ProductStock;
use Filament\Widgets\TableWidget;
use Filament\Tables\Columns\TextColumn;

class LowStockAlert extends TableWidget
{
    protected static ?string $heading = 'Daftar Stok Produk';

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return ProductStock::with('product')->select('*'); // tampilkan semua
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('product.name')
                ->label('Produk'),

            TextColumn::make('current_stock')
                ->label('Stok Saat Ini'),

            TextColumn::make('minimum_stock')
                ->label('Stok Minimum'),

            TextColumn::make('status')
                ->label('Status')
                ->getStateUsing(function (ProductStock $record) {
                    return $record->current_stock <= $record->minimum_stock
                        ? 'Menipis'
                        : 'Aman';
                })
                ->color(fn($state) => $state === 'Menipis' ? 'danger' : 'success'),
        ];
    }
}
