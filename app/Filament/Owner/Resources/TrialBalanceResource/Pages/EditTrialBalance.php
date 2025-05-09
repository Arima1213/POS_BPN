<?php

namespace App\Filament\Owner\Resources\TrialBalanceResource\Pages;

use App\Filament\Owner\Resources\TrialBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrialBalance extends EditRecord
{
    protected static string $resource = TrialBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
