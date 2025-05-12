<?php

namespace App\Filament\Resources\ProcurementResource\Pages;

use App\Filament\Resources\HppstatsResource\Widgets\HppStats;
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

    // Ini untuk menampilkan widget di atas tabel
    protected function getHeaderWidgets(): array
    {
        return [
            HppStats::class,
        ];
    }
}