<?php

namespace App\Filament\Owner\Resources\TransactionLogResource\Pages;

use App\Filament\Owner\Resources\TransactionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactionLogs extends ListRecords
{
    protected static string $resource = TransactionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
