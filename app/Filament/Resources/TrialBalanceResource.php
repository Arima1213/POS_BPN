<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrialBalanceResource\Pages;
use App\Filament\Resources\TrialBalanceResource\RelationManagers;
use App\Models\TrialBalance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TrialBalanceResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $label = 'Neraca Saldo';
    protected static ?string $pluralLabel = 'Neraca Saldo';
    protected static ?string $slug = 'neraca-saldo';
    protected static ?string $navigationLabel = 'Neraca Saldo';
    protected static ?string $navigationGroup = 'Akuntansi';
    protected static ?int $navigationSort = 3;


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
            'index' => Pages\TrialBalance::route('/'),
        ];
    }
}