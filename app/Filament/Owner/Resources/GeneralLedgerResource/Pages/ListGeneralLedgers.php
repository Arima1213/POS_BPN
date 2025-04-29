<?php

namespace App\Filament\Owner\Resources\GeneralLedgerResource\Pages;

use App\Filament\Owner\Resources\GeneralLedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralLedgers extends ListRecords
{
    protected static string $resource = GeneralLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
