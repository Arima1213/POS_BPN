<?php

namespace App\Filament\Owner\Resources\TransactionResource\Pages;

use App\Filament\Owner\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Transaksi'),
        ];
    }

    public function getTitle(): string
    {
        return 'Ubah Transaksi';
    }
}
