<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeStatementResource\Pages;
use App\Filament\Resources\IncomeStatementResource\RelationManagers;
use App\Models\IncomeStatement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IncomeStatementResource extends Resource
{

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Laba Rugi';
    protected static ?string $pluralLabel = 'Laba Rugi';
    protected static ?string $slug = 'income-statement';
    protected static ?string $navigationLabel = 'Laba Rugi';
    protected static ?string $navigationGroup = 'Akuntansi';


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncomeStatements::route('/'),
        ];
    }
}
