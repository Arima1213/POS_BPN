<?php

namespace App\Filament\Owner\Resources\TransactionResource\Pages;

use App\Filament\Owner\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Transaksi'),
        ];
    }
}
