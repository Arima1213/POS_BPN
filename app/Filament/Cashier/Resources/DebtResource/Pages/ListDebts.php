<?php

namespace App\Filament\Cashier\Resources\DebtResource\Pages;

use App\Filament\Cashier\Resources\DebtResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDebts extends ListRecords
{
    protected static string $resource = DebtResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
