<?php

namespace App\Filament\Owner\Resources\IncomeStatementResource\Pages;

use App\Filament\Owner\Resources\IncomeStatementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIncomeStatements extends ListRecords
{
    protected static string $resource = IncomeStatementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
