<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\TrialBalanceResource\Pages;
use Filament\Resources\Resource;

class TrialBalanceResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $label = 'Neraca Saldo';
    protected static ?string $pluralLabel = 'Neraca Saldo';
    protected static ?string $slug = 'neraca-saldo';
    protected static ?string $navigationLabel = 'Neraca Saldo';
    protected static ?string $navigationGroup = 'Laporan Keuangan';

    protected static ?int $navigationSort = 3;
    public static function getPages(): array
    {
        return [
            'index' => Pages\TrialBalance::route('/'),
        ];
    }
}
