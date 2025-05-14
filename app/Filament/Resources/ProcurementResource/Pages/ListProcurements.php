<?php

namespace App\Filament\Resources\ProcurementResource\Pages;

use App\Filament\Resources\HppResource\Widgets\HppSaatIni;
use App\Filament\Resources\ProcurementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProcurements extends ListRecords
{
    protected static string $resource = ProcurementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\HppStatsResource\Widgets\HppWidget::class,
        ];
    }
}
