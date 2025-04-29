<?php

namespace App\Filament\Owner\Resources\TransactionLogResource\Pages;

use App\Filament\Exports\TransactionLogOwnerExporter;
use App\Filament\Owner\Resources\TransactionLogResource;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListTransactionLogs extends ListRecords
{
    protected static string $resource = TransactionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ExportAction::make()
            //     ->exporter(TransactionLogOwnerExporter::class),
        ];
    }
}
