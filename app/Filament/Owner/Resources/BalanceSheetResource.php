<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\BalanceSheetResource\Pages;
use App\Filament\Owner\Resources\BalanceSheetResource\RelationManagers;
use App\Models\BalanceSheet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BalanceSheetResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $label = 'Posisi Keuangan';
    protected static ?string $pluralLabel = 'Posisi Keuangan';
    protected static ?string $slug = 'posisi-keuangan';
    protected static ?string $navigationLabel = 'Posisi Keuangan';
    protected static ?string $navigationGroup = 'Laporan Keuangan';
    protected static ?int $navigationSort = 4;


    public static function getPages(): array
    {
        return [
            'index' => Pages\BalanceSheets::route('/'),
        ];
    }
}
