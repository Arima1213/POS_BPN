<?php

namespace App\Filament\Resources\CompanyReceivablesResource\Pages;

use App\Filament\Resources\CompanyReceivablesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyReceivables extends EditRecord
{
    protected static string $resource = CompanyReceivablesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
