<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowProductLandingResource\Pages;
use App\Models\ShowProductLanding;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShowProductLandingResource extends Resource
{
    protected static ?string $model = ShowProductLanding::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Kelola Produk Landing';
    protected static ?string $navigationGroup = 'Landing Page';
    protected static ?string $pluralLabel = 'Kelola Produk Landing';
    protected static ?string $slug = 'kelola-produk-Landing';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Tambahkan field sesuai kebutuhan
                \Filament\Forms\Components\Select::make('tipe')
                    ->options([
                        'produk' => 'Produk',
                        'jasa' => 'Jasa',
                    ])
                    ->required(),
                \Filament\Forms\Components\Select::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak' => 'Tidak Aktif',
                    ])
                    ->required(),
                \Filament\Forms\Components\TextInput::make('product_id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable(),
                Tables\Columns\TextColumn::make('tipe')->label('Tipe')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->enum([
                        'aktif' => 'Aktif',
                        'tidak' => 'Tidak Aktif',
                    ])
                    ->colors([
                        'success' => 'aktif',
                        'danger' => 'tidak',
                    ]),
                Tables\Columns\TextColumn::make('product_id')->label('Product ID'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat'),
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
            'index' => Pages\ListShowProductLandings::route('/'),
            'create' => Pages\CreateShowProductLanding::route('/create'),
            'edit' => Pages\EditShowProductLanding::route('/{record}/edit'),
        ];
    }
}
