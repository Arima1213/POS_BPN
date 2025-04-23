<?php

namespace App\Filament\Resources\CompanyReceivablesResource\Pages;

use App\Filament\Resources\CompanyReceivablesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyReceivables extends ListRecords
{
    protected static string $resource = CompanyReceivablesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
