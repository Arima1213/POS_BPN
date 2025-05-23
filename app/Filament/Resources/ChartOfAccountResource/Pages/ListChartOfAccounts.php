<?php

namespace App\Filament\Resources\ChartOfAccountResource\Pages;

use App\Filament\Exports\ChartOfAccountExporter;
use App\Filament\Resources\ChartOfAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChartOfAccounts extends ListRecords
{
    protected static string $resource = ChartOfAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah Akun'),
            Actions\ExportAction::make()->label('Ekspor Akun')
                ->exporter(ChartOfAccountExporter::class),
        ];
    }
}
