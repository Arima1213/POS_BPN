<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShowProductLandingResource\Pages;
use App\Models\Product;
use App\Models\Services;
use App\Models\ShowProductLanding;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ShowProductLandingResource extends Resource
{
    protected static ?string $model = ShowProductLanding::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationLabel = 'Produk Ditampilkan';
    protected static ?string $navigationGroup = 'Landing Page';
    protected static ?string $pluralLabel = 'Produk Ditampilkan';
    protected static ?string $slug = 'Produk-Ditampilkan';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'produk' => 'Produk',
                        'jasa' => 'Jasa',
                    ])
                    ->required()
                    ->reactive(), // agar product_id ikut berubah

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'tidak' => 'Tidak Aktif',
                    ])
                    ->required(),

                Select::make('product_id')
                    ->label('Produk/Jasa')
                    ->required()
                    ->placeholder('Pilih Produk atau Jasa')
                    ->options(function ($get) {
                        $tipe = $get('tipe');

                        if ($tipe === 'produk') {
                            return Product::pluck('name', 'id')->toArray();
                        }

                        if ($tipe === 'jasa') {
                            return Services::pluck('name', 'id')->toArray();
                        }

                        return [];
                    })
                    ->disabled(fn($get) => !$get('tipe'))
                    ->reactive(), // agar field ini merespon perubahan dari field lain
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['product', 'service']))
            ->columns([
                TextColumn::make('id')->sortable(),

                TextColumn::make('tipe')->label('Tipe')->sortable(),

                TextColumn::make('item_name')->label('Nama Produk/Jasa'),
                TextColumn::make('item_price')
                    ->label('Harga')
                    ->formatStateUsing(
                        fn($state) =>
                        $state !== null ? 'Rp ' . number_format($state, 0, ',', '.') : '-'
                    ),


                TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn($state) => $state === 'aktif' ? 'Aktif' : 'Tidak Aktif')
                    ->badge()
                    ->color(fn($state) => $state === 'aktif' ? 'success' : 'danger'),

                TextColumn::make('created_at')->dateTime()->label('Dibuat'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Action::make('toggleStatus')
                    ->label(fn($record) => $record->status === 'aktif' ? 'Nonaktifkan' : 'Aktifkan')
                    ->color(fn($record) => $record->status === 'aktif' ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading('Ubah Status')
                    ->modalSubheading(
                        fn($record) =>
                        "Apakah Anda yakin ingin " .
                            ($record->status === 'aktif' ? 'menonaktifkan' : 'mengaktifkan') .
                            " item ini?"
                    )
                    ->action(function ($record) {
                        $record->update([
                            'status' => $record->status === 'aktif' ? 'tidak' : 'aktif',
                        ]);
                    }),
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