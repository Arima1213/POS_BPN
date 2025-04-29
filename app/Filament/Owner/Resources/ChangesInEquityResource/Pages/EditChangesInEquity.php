<?php

namespace App\Filament\Owner\Resources\ChangesInEquityResource\Pages;

use App\Filament\Owner\Resources\ChangesInEquityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChangesInEquity extends EditRecord
{
    protected static string $resource = ChangesInEquityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
