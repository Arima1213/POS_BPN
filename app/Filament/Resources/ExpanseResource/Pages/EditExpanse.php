<?php

namespace App\Filament\Resources\ExpanseResource\Pages;

use App\Filament\Resources\ExpanseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExpanse extends EditRecord
{
    protected static string $resource = ExpanseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Tambah Beban'),
        ];
    }
    public function getTitle(): string
    {
        return 'Ubah Beban';
    }
}
