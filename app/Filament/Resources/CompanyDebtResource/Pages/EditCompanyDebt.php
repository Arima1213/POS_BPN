<?php

namespace App\Filament\Resources\CompanyDebtResource\Pages;

use App\Filament\Resources\CompanyDebtResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyDebt extends EditRecord
{
    protected static string $resource = CompanyDebtResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Utang'),
        ];
    }

    public function getTitle(): string
    {
        return 'Ubah Utang';
    }
}
