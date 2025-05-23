<?php

namespace App\Filament\Resources\ProductStocksResource\Pages;

use App\Filament\Resources\ProductStocksResource;
use App\Models\StockHistory;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductStocks extends EditRecord
{
    protected static string $resource = ProductStocksResource::class;

    public function getTitle(): string
    {
        return 'Ubah Stok Produk';
    }


    // Variabel untuk menyimpan stok lama
    protected int $originalStock = 0;

    protected function beforeSave(): void
    {
        // Simpan stok sebelum diubah
        $this->originalStock = $this->record->getOriginal('current_stock');
    }

    protected function afterSave(): void
    {
        $newStock = $this->record->current_stock;

        if ($this->originalStock != $newStock) {
            $quantityChange = $newStock - $this->originalStock;

            StockHistory::create([
                'product_id' => $this->record->product_id,
                'type' => $quantityChange > 0 ? 'in' : 'out', // in jika nambah, out jika berkurang
                'quantity' => abs($quantityChange),
                'note' => 'Stock updated via edit form',
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
