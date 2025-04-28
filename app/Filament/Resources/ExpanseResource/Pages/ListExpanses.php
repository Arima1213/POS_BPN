<?php

namespace App\Filament\Resources\ExpanseResource\Pages;

use App\Filament\Exports\ExpanseExporter;
use App\Filament\Resources\ExpanseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExpanses extends ListRecords
{
    protected static string $resource = ExpanseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ExportAction::make()
                ->exporter(ExpanseExporter::class)
        ];
    }
}