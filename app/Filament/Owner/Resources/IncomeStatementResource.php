<?php

namespace App\Filament\Owner\Resources;

use App\Filament\Owner\Resources\IncomeStatementResource\Pages;
use App\Models\JournalEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IncomeStatementResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $label = 'Laba Rugi';
    protected static ?string $pluralLabel = 'Laba Rugi';
    protected static ?string $slug = 'laba-rugi';
    protected static ?string $navigationLabel = 'Laba Rugi';
    protected static ?string $navigationGroup = 'Akuntansi';
    protected static ?int $navigationSort = 6;


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\IncomeStatement::route('/'),
        ];
    }
}
