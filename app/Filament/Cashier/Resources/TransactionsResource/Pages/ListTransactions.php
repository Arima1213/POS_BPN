<?php

namespace App\Filament\Cashier\Resources\TransactionsResource\Pages;

use App\Filament\Cashier\Resources\TransactionsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
