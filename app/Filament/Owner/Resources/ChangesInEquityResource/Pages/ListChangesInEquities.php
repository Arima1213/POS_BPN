<?php

namespace App\Filament\Cashier\Resources\ChangesInEquityResource\Pages;

use App\Filament\Owner\Resources\ChangesInEquityResource;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChangesInEquities extends ListRecords
{
    protected static string $resource = ChangesInEquityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
