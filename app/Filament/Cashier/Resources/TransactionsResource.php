<?php

namespace App\Filament\Cashier\Resources;

use App\Filament\Cashier\Resources\TransactionsResource\Pages;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Services;
use App\Models\Transactions;
use App\Models\Debt;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Redirect;

class TransactionsResource extends Resource
{
    protected static ?string $model = Transactions::class;

    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $pluralLabel = 'Kelola Transaksi';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $slug = 'kelola-transaksi';


    public static function getNavigationSort(): int
    {
        return 1;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()
                ->schema([
                    Split::make([
                        Group::make([
                            Section::make('Informasi Total')
                                ->schema([
                                    TextInput::make('code')
                                        ->label('Kode Transaksi')
                                        ->default(fn() => 'TRX-' . now()->format('dmy') . '-' . strtoupper(Str::random(5)))
                                        ->disabled()
                                        ->dehydrated(true)
                                        ->required(),

                                    TextInput::make('total')
                                        ->label('Total')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated()
                                        ->default(0)
                                        ->reactive()
                                        ->placeholder(function (Set $set, Get $get) {
                                            $total = collect($get('details'))->pluck('subtotal')->sum();
                                            $set('total', $total ?? 0);
                                        }),

                                    TextInput::make('paid_amount')
                                        ->label('Uang Pembeli')
                                        ->numeric()
                                        ->required()
                                        ->debounce(500) // atau ->lazy() jika ingin tunggu sampai blur
                                        ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                            $total = $get('total') ?? 0;
                                            $set('change_amount', intval($state - $total));
                                        }),


                                    TextInput::make('change_amount')
                                        ->label('Kembalian')
                                        ->numeric()
                                        ->disabled()
                                        ->dehydrated()
                                        ->default(0)
                                        ->hint(fn(Get $get) => ($get('change_amount') ?? 0) < 0 ? '⚠️ Uang kurang, akan dicatat sebagai hutang.' : null)
                                        ->hintColor(fn(Get $get) => ($get('change_amount') ?? 0) < 0 ? 'danger' : 'success'),
                                ]),

                            Section::make('Informasi Customer')
                                ->schema([
                                    Toggle::make('add_new_customer')
                                        ->label('Tambah Customer Baru?')
                                        ->reactive(),

                                    Select::make('customer_id')
                                        ->label('Customer')
                                        ->searchable()
                                        ->preload()
                                        ->options(fn() => Customer::pluck('name', 'id'))
                                        ->visible(fn(Get $get) => $get('add_new_customer') === false)
                                        ->required(),

                                    Group::make([
                                        TextInput::make('new_customer_name')
                                            ->label('Nama Customer')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('new_customer_phone')
                                            ->label('Nomor Telepon')
                                            ->required()
                                            ->maxLength(15),
                                    ])
                                        ->visible(fn(Get $get) => $get('add_new_customer') === true),

                                ]),
                        ]),
                        Repeater::make('details')
                            ->columnSpan(2)
                            ->label('Detail Transaksi')
                            ->relationship('details')
                            ->schema([
                                Radio::make('item_type')
                                    ->label('Tipe')
                                    ->options([
                                        'product' => 'Product',
                                        'service' => 'Service',
                                    ])
                                    ->live()
                                    ->default('product')
                                    ->afterStateUpdated(fn($state, callable $set) => $set('item_id', null))
                                    ->required(),

                                Select::make('item_id')
                                    ->label('Produk / Jasa')
                                    ->options(function (callable $get) {
                                        return $get('item_type') === 'service'
                                            ? Services::pluck('name', 'id')
                                            : Product::pluck('name', 'id');
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $type = $get('item_type');
                                        if ($type === 'product') {
                                            $product = Product::find($state);
                                            if ($product) {
                                                $set('price', $product->price);
                                                $set('qty_label', 'qty');
                                            }
                                        } elseif ($type === 'service') {
                                            $service = Services::with('unit')->find($state);
                                            if ($service) {
                                                $set('price', $service->price);
                                                $set('qty_label', $service->unit->short ?? 'unit');
                                            }
                                        }
                                    })
                                    ->required(),

                                TextInput::make('qty_label')
                                    ->default('qty')
                                    ->disabled()
                                    ->visible(false)
                                    ->dehydrated(false),

                                TextInput::make('price')
                                    ->label('Harga')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),

                                TextInput::make('quantity')
                                    ->label(fn(callable $get) => 'Jumlah (' . ($get('qty_label') ?? 'qty') . ')')
                                    ->numeric()
                                    ->reactive()
                                    ->debounce(1000)
                                    ->afterStateUpdated(function (Set $set, $state, Get $get) {
                                        $price = $get('price') ?? 0;
                                        $set('subtotal', intval($price * $state));
                                    })
                                    ->required(),

                                TextInput::make('subtotal')
                                    ->label('Subtotal')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                            ])
                            ->columns(2)
                            ->createItemButtonLabel('Tambah Item')
                            ->defaultItems(1)
                            ->required()
                            ->reactive(),
                    ])
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode'),
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer'),
                Tables\Columns\TextColumn::make('total')
                    ->money('IDR')
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->money('IDR')
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('change_amount')
                    ->money('IDR')
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\Action::make('faktur')
                    ->label('Faktur')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn($record) => route('transactions.download.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('print_receipt')
                    ->label('Print Struk')
                    ->icon('heroicon-o-printer')
                    ->action(function ($record) {
                        // Redirect ke route cetak struk thermal
                        return Redirect::to(route('transactions.print.receipt', $record));
                    })
                    ->requiresConfirmation()
                    ->color('success'),
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