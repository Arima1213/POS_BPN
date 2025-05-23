<?php

namespace App\Filament\Owner\Resources\TransactionLogResource\Pages;

use App\Filament\Owner\Resources\TransactionLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransactionLog extends EditRecord
{
    protected static string $resource = TransactionLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Transaksi'),
        ];
    }
}
