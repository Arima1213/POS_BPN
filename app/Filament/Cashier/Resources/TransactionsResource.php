<?php

namespace App\Filament\Cashier\Resources;

use App\Filament\Cashier\Resources\TransactionsResource\Pages;
use App\Models\Customer;
use Filament\Forms\Get;
use App\Models\Product;
use App\Models\Service;
use App\Models\Services;
use App\Models\Transactions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Hidden;
use App\Models\Unit;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;

class TransactionsResource extends Resource
{
    protected static ?string $model = Transactions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
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
                                    $set('quantity', 1);
                                    $set('qty_label', 'qty');
                                    $set('subtotal', $product->price);
                                }
                            } elseif ($type === 'service') {
                                $service = Services::with('unit')->find($state);
                                if ($service) {
                                    $set('price', $service->price);
                                    $set('quantity', 1);
                                    $set('qty_label', $service->unit->short_name ?? 'unit');
                                    $set('subtotal', $service->price);
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
                        ->default(1)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $price = $get('price') ?? 0;
                            $set('subtotal', $price * (int) $state);
                        })
                        ->required(),

                    TextInput::make('subtotal')
                        ->label('Subtotal')
                        ->disabled()
                        ->dehydrated()
                        ->required(),
                ])
                ->columns(2)
                ->createItemButtonLabel('Tambah Item')
                ->defaultItems(1)
                ->required()
                ->reactive()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    $total = collect($state)->sum('subtotal');
                    $set('total', $total);
                }),

            Section::make('Informasi Total')
                ->schema([
                    TextInput::make('code')
                        ->label('Kode Transaksi')
                        ->default(fn() => 'TRX-' . strtoupper(Str::random(8)))
                        ->disabled()
                        ->dehydrated(true)
                        ->required(),

                    TextInput::make('total')
                        ->label('Total')
                        ->numeric()
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->reactive()
                        ->default(0),

                    TextInput::make('paid_amount')
                        ->label('Uang Pembeli')
                        ->numeric()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $total = $get('total') ?? 0;
                            $change = $state - $total;
                            $set('change_amount', $change);
                        }),

                    TextInput::make('change_amount')
                        ->label('Kembalian')
                        ->numeric()
                        ->disabled()
                        ->dehydrated()
                        ->required()
                        ->default(0)
                        ->hint(fn(Get $get) => ($get('change_amount') ?? 0) < 0 ? '⚠️ Uang kurang, akan dicatat sebagai hutang.' : null)
                        ->hintColor(fn(Get $get) => ($get('change_amount') ?? 0) < 0 ? 'danger' : 'success'),
                ]),


            Section::make('Informasi Customer')
                ->schema([
                    // Tombol toggle untuk menampilkan form input customer baru
                    Toggle::make('add_new_customer')
                        ->label('Tambah Customer Baru?')
                        ->reactive(),

                    // Jika TIDAK menambah customer, tampilkan dropdown seperti biasa
                    Select::make('customer_id')
                        ->label('Customer')
                        ->searchable()
                        ->preload()
                        ->options(fn() => Customer::pluck('name', 'id'))
                        ->visible(fn(Get $get) => $get('add_new_customer') === false)
                        ->required(),

                    // Jika tambah customer, munculkan field input baru
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

                    // Hidden field untuk memanipulasi customer_id setelah dibuat
                    Hidden::make('customer_id')
                        ->dehydrated(fn(Get $get) => $get('add_new_customer') === true),
                ]),
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
            ->filters([])
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

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['add_new_customer'])) {
            $customer = Customer::create([
                'name' => $data['new_customer_name'],
                'phone' => $data['new_customer_phone'],
            ]);

            $data['customer_id'] = $customer->id;
        }

        return $data;
    }
}