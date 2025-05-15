<?php

namespace App\Filament\Owner\Resources\DashboardPageResource\Pages;

use App\Filament\Owner\Resources\DashboardPageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDashboardPage extends EditRecord
{
    protected static string $resource = DashboardPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
