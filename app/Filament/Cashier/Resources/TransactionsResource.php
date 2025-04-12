<?php

namespace App\Filament\Cashier\Resources;

use App\Filament\Cashier\Resources\TransactionsResource\Pages;
use App\Models\Customer;
use App\Models\Transactions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class TransactionsResource extends Resource
{
    protected static ?string $model = Transactions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->label('Kode Transaksi')
                ->default(fn() => 'TRX-' . strtoupper(Str::random(8)))
                ->disabled()
                ->dehydrated(true)
                ->required(),

            Forms\Components\Select::make('customer_id')
                ->label('Customer')
                ->searchable()
                ->preload()
                ->options(fn() => Customer::pluck('name', 'id'))
                ->required(),

            Forms\Components\TextInput::make('total')
                ->label('Total')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('paid_amount')
                ->label('Uang Pembeli')
                ->numeric()
                ->required(),

            Forms\Components\TextInput::make('change_amount')
                ->label('Kembalian')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Kode'),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                Tables\Columns\TextColumn::make('total')->money('IDR'),
                Tables\Columns\TextColumn::make('paid_amount')->label('Uang Pembeli')->money('IDR'),
                Tables\Columns\TextColumn::make('change_amount')->label('Kembalian')->money('IDR'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Tanggal'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransactions::route('/create'),
            'edit' => Pages\EditTransactions::route('/{record}/edit'),
        ];
    }
}