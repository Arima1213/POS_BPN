<?php

namespace App\Filament\Cashier\Resources\TransactionsResource\Pages;

use App\Filament\Cashier\Resources\TransactionsResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactions extends CreateRecord
{
    protected static string $resource = TransactionsResource::class;
}
