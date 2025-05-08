<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Models\JournalEntry;
use App\Models\ProductStock;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductResource;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        ProductStock::create([
            'product_id' => $this->record->id,
            'current_stock' => 0, // Default initial stock
            'minimum_stock' => 0, // Default minimum stock
        ]);
    }
}