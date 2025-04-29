<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Resources\ChangesInEquityResource\Pages;
use App\Filament\Resources\ChangesInEquityResource\RelationManagers;
use App\Models\ChangesInEquity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
