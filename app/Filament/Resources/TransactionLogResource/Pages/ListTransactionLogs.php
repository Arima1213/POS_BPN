<?php

namespace App\Filament\Resources\TransactionLogResource\Pages;

use App\Filament\Exports\TransactionLogExporter;
use App\Filament\Resources\TransactionLogResource;
use Filament\Actions;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;

class ListTransactionLogs extends ListRecords
{
    protected static string $resource = TransactionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\ExportAction::make()
                ->exporter(TransactionLogExporter::class)
        ];
    }
}
