<?php

namespace App\Filament\Resources\ProcurementResource\Pages;

use App\Filament\Resources\ProcurementResource;
use App\Models\Stock;
use App\Models\StockHistory;
use Filament\Resources\Pages\CreateRecord;

class CreateProcurement extends CreateRecord
{
    protected static string $resource = ProcurementResource::class;

    protected function afterCreate(): void
    {
        $procurement = $this->record;

        // Update stock
        $stock = Stock::firstOrCreate(
            ['product_id' => $procurement->product_id],
            ['quantity' => 0, 'minimum_stock' => 0]
        );

        $stock->increment('quantity', $procurement->quantity);

        // Simpan ke stock history
        StockHistory::create([
            'product_id' => $procurement->product_id,
            'type' => 'in', // masuk
            'quantity' => $procurement->quantity,
            'note' => 'Pengadaan Barang #' . $procurement->id,
        ]);
    }
}