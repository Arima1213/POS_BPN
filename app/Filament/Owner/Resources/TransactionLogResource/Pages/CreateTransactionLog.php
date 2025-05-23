<?php

namespace App\Filament\Owner\Resources\TransactionLogResource\Pages;

use App\Filament\Owner\Resources\TransactionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTransactionLog extends CreateRecord
{
    protected static string $resource = TransactionLogResource::class;

    public function getTitle(): string
    {
        return 'Tambah Transaksi';
    }
}
