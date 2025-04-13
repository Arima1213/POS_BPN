<?php

namespace App\Filament\Resources\ProductStocksResource\Pages;

use App\Filament\Resources\ProductStocksResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductStocks extends EditRecord
{
    protected static string $resource = ProductStocksResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
