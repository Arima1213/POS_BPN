<?php

namespace App\Filament\Cashier\Resources;

use App\Filament\Cashier\Resources\DebtResource\Pages;
use App\Models\Debt;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('transaction.id')
                    ->label('Transaction')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->label('Due Date')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created At')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDebts::route('/'),
        ];
    }
}
