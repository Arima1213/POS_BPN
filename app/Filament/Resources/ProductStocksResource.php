<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductStocksResource\Pages;
use App\Models\ProductStock;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Str;

class ProductStocksResource extends Resource
{
    protected static ?string $model = ProductStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube-transparent';
    protected static ?string $navigationLabel = 'Product Stocks';
    protected static ?string $navigationGroup = 'Inventory Management';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('current_stock')
                    ->label('Current Stock')
                    ->sortable()
                    ->badge()
                    ->color(fn($state, $record) =>
                    $record->current_stock <= $record->minimum_stock
                        ? 'danger'
                        : 'success'),

                TextColumn::make('minimum_stock')
                    ->label('Minimum')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->tooltip('Edit stock information'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductStocks::route('/'),
            'create' => Pages\CreateProductStocks::route('/create'),
            'edit' => Pages\EditProductStocks::route('/{record}/edit'),
        ];
    }
}