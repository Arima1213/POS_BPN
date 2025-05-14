<?php

namespace App\Filament\Resources\OurTeamResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\OurTeamResource;

class CreateOurTeam extends CreateRecord
{
    protected static string $resource = OurTeamResource::class;
    public function getTitle(): string
    {
        return 'Tambah Anggota Tim';
    }
}
