<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $navigationGroup = 'Produk & Jasa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->options(
                        Category::all()->pluck('name', 'id')
                    )
                    ->searchable()
                    ->required()
                    ->placeholder('Select a category'),

                Forms\Components\TextInput::make('itemcode')
                    ->label('Item Code')
                    ->required()
                    ->disabled()
                    ->dehydrated(true)
                    ->default(fn() => 'PRD-' . strtoupper(uniqid()))
                    ->placeholder(fn() => 'PRD-' . strtoupper(uniqid())),

                Forms\Components\FileUpload::make('image')
                    ->label('Product Image')
                    ->image()
                    ->required()
                    ->directory('products')
                    ->visibility('public')
                    ->placeholder('Upload product image'),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Enter product name'),

                Forms\Components\TextInput::make('brand')
                    ->maxLength(255)
                    ->required()
                    ->placeholder('Enter brand name'),

                Forms\Components\TextInput::make('itemweight')
                    ->numeric()
                    ->label('Item Weight (KG)')
                    ->required()
                    ->placeholder('Enter item weight in grams'),

                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->placeholder('Enter product description'),

                Forms\Components\TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('Rp')
                    ->placeholder('Enter product price'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('itemcode')->label('Code')->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->disk('public')
                    ->circular()
                    ->height(50)
                    ->width(50),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('brand'),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->sortable(),
                Tables\Columns\TextColumn::make('itemweight')->label('Weight')->suffix(' Kg'),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->label('Created'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
