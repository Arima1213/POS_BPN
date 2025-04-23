<?php

namespace App\Filament\Resources\GeneralLedgerResource\Pages;

use App\Filament\Resources\GeneralLedgerResource;
use App\Models\JournalEntry;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeneralLedgers extends ListRecords
{
    protected static string $resource = JournalEntry::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
