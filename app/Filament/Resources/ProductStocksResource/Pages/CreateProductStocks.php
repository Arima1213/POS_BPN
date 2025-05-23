<?php

namespace App\Filament\Resources\ProductStocksResource\Pages;

use App\Filament\Resources\ProductStocksResource;
use App\Models\StockHistory;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductStocks extends CreateRecord
{
    protected static string $resource = ProductStocksResource::class;

    public function getTitle(): string
    {
        return 'Tambah Stok Produk';
    }
}
