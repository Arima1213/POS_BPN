<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductStocksResource\Pages;
use App\Models\ProductStock;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class ProductStocksResource extends Resource
{
    protected static ?string $model = ProductStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Kelola Stok Produk';
    protected static ?string $navigationGroup = 'Inventaris';
    protected static ?string $pluralLabel = 'Kelola Stok';
    protected static ?string $slug = 'kelola-stok';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->label('Product')
                    ->options(Product::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->hidden(),

                TextInput::make('current_stock')
                    ->label('Current Stock')
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->hint(fn(string $context): ?string => $context === 'edit'
                        ? 'Stok akan otomatis bertambah jika ada pengadaan barang.'
                        : null),

                TextInput::make('minimum_stock')
                    ->label('Minimum Stock')
                    ->numeric()
                    ->minValue(0)
                    ->required(),
            ]);
    }

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
                TextColumn::make('stock_opname')
                    ->label('Stock Opname (Last Month)')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        // Hitung total pengadaan bulan ini
                        $startOfMonth = now()->startOfMonth();
                        $endOfMonth = now()->endOfMonth();

                        $procurementThisMonth = \App\Models\Procurement::where('product_id', $record->product_id)
                            ->whereBetween('procurement_date', [$startOfMonth, $endOfMonth])
                            ->sum('quantity');

                        // Hitung stok bulan lalu: current_stock - pengadaan bulan ini
                        return $record->current_stock - $procurementThisMonth;
                    }),

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
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view_history')
                    ->label('Stock History')
                    ->icon('heroicon-o-clock')
                    ->modalHeading('Stock Change History')
                    ->modalSubmitAction(false) // tidak perlu tombol submit
                    ->modalCancelActionLabel('Close') // tombol close saja
                    ->modalContent(function ($record) {
                        return view('product-stock-history-modal', [
                            'histories' => $record->stockHistories()->latest()->get(),
                        ]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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