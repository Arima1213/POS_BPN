<?php

namespace App\Filament\Owner\Resources\SalaryResource\Pages;

use App\Filament\Owner\Resources\SalaryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalary extends EditRecord
{
    protected static string $resource = SalaryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus Gaji'),
        ];
    }

    public function getTitle(): string
    {
        return 'Ubah Gaji';
    }
}
