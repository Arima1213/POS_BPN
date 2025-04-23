<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GeneralLedgerResource\Pages;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class GeneralLedgerResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Buku Besar';
    protected static ?string $pluralLabel = 'Buku Besar';
    protected static ?string $slug = 'general-ledger';
    protected static ?string $navigationLabel = 'Buku Besar';
    protected static ?string $navigationGroup = 'Akuntansi';

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGeneralLedgers::route('/'),
        ];
    }
}
