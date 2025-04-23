<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncomeStatementResource\Pages;
use App\Models\JournalEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class IncomeStatementResource extends Resource
{
    protected static ?string $model = JournalEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $label = 'Laba Rugi';
    protected static ?string $pluralLabel = 'Laba Rugi';
    protected static ?string $slug = 'income-statement';
    protected static ?string $navigationLabel = 'Laba Rugi';
    protected static ?string $navigationGroup = 'Akuntansi';

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
