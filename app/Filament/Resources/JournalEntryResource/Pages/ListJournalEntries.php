<?php

namespace App\Filament\Resources\JournalEntryResource\Pages;

use App\Filament\Exports\JournalEntryExporter;
use App\Filament\Resources\JournalEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJournalEntries extends ListRecords
{
    protected static string $resource = JournalEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ExportAction::make()
                ->exporter(JournalEntryExporter::class)
        ];
    }
}
