<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\ChangesInEquityResource\Pages;
use Filament\Resources\Resource;

class ChangesInEquityResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Perubahan Ekuitas';
    protected static ?string $pluralLabel = 'Perubahan Ekuitas';
    protected static ?string $slug = 'perubahan-ekuitas';
    protected static ?string $navigationLabel = 'Perubahan Ekuitas';
    protected static ?string $navigationGroup = 'Akuntansi';
    protected static ?int $navigationSort = 5;


    public static function getPages(): array
    {
        return [
            'index' => Pages\ChangesInEquity::route('/'),
        ];
    }
}
