<?php

namespace App\Filament\Resources\OwnerTransactionResource\Pages;

use App\Filament\Resources\OwnerTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOwnerTransaction extends EditRecord
{
    protected static string $resource = OwnerTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
