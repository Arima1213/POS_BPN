<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BalanceSheetResource\Pages;
use App\Filament\Resources\BalanceSheetResource\RelationManagers;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPages(): array
    {
        return [
            'index' => Pages\BalanceSheets::route('/'),
        ];
    }
}
