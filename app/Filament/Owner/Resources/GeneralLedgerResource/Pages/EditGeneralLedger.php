<?php

namespace App\Filament\Owner\Resources\GeneralLedgerResource\Pages;

use App\Filament\Owner\Resources\GeneralLedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeneralLedger extends EditRecord
{
    protected static string $resource = GeneralLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
