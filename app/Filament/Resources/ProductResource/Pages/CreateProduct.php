<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductStock;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    protected function afterCreate(): void
    {
        // Cek jika stok produk belum ada untuk product_id tersebut
        if (!ProductStock::where('product_id', $this->record->id)->exists()) {
            ProductStock::create([
                'product_id' => $this->record->id,
                'current_stock' => 0,      // default awal 0
                'minimum_stock' => 0,      // bisa kamu sesuaikan
            ]);
        }
    }
}
