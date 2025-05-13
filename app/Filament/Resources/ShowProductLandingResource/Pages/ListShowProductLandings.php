<?php

namespace App\Filament\Resources\ShowProductLandingResource\Pages;

use App\Filament\Resources\ShowProductLandingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShowProductLandings extends ListRecords
{
    protected static string $resource = ShowProductLandingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
