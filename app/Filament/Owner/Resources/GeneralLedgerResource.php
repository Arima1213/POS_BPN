<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\GeneralLedgerResource\Pages;
use App\Models\JournalEntry;
use Filament\Resources\Resource;

class GeneralLedgerResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $label = 'Buku Besar';
    protected static ?string $pluralLabel = 'Buku Besar';
    protected static ?string $slug = 'buku-besar';
    protected static ?string $navigationLabel = 'Buku Besar';
    protected static ?string $navigationGroup = 'Laporan Keuangan';
    protected static ?int $navigationSort = 2;


    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\GeneralLedger::route('/'),
        ];
    }
}
