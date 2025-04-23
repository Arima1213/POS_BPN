<?php

namespace App\Filament\Resources\TrialBalanceResource\Pages;

use App\Filament\Resources\TrialBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrialBalances extends ListRecords
{
    protected static string $resource = TrialBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
