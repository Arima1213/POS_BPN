<?php

namespace App\Filament\Resources\GeneralLedgerResource\Pages;

use App\Filament\Resources\GeneralLedgerResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralLedgers extends ListRecords
{
    protected static string $resource = GeneralLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
