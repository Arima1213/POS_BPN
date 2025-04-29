<?php

namespace App\Filament\Owner\Resources\BalanceSheetResource\Pages;

use App\Filament\Owner\Resources\BalanceSheetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBalanceSheets extends ListRecords
{
    protected static string $resource = BalanceSheetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
