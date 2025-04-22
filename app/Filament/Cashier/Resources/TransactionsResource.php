<?php

namespace App\Filament\Cashier\Resources;

use App\Filament\Cashier\Resources\TransactionsResource\Pages;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Services;
use App\Models\Transactions;
use App\Models\Units;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Livewire\Component;
use Filament\Forms\Form;
use Illuminate\Support\Collection;

class TransactionsResource extends Resource
{
    protected static ?string $model = Transactions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationSort(): int
    {
        return 1;
    }

    public static function form(Form $form): Form
    {
        $products = Product::select(['id', 'name', 'price', 'image'])->get()->mapWithKeys(fn($product) => [
            'product-' . $product->id => [
                'label' => $product->name,
                'image' => $product->image,
                'price' => $product->price,
                'type' => 'product'
            ]
        ]);

        $services = Services::with('unit')->get()->mapWithKeys(fn($service) => [
            'service-' . $service->id => [
                'label' => $service->name,
                'image' => $service->image,
                'price' => $service->price,
                'unit' => $service->unit?->short ?? 'unit',
                'type' => 'service'
            ]
        ]);

        $items = $products->merge($services);

        return $form->schema([
            Select::make('selected_item')
                ->label('Pilih Barang / Jasa')
                ->options(
                    collect($items)->map(fn($item, $key) => $item['label'])->toArray()
                )
                ->reactive()
                ->live()
                ->afterStateUpdated(function ($state, $set, $get) use ($items) {
                    if (!$state) return;

                    $data = $items[$state];
                    $details = collect($get('details'));

                    $existing = $details->firstWhere(fn($item) => $item['item_id'] === explode('-', $state)[1] && $item['item_type'] === $data['type']);

                    if ($existing) {
                        $details = $details->map(function ($item) use ($state, $data) {
                            if ($item['item_id'] === explode('-', $state)[1] && $item['item_type'] === $data['type']) {
                                $item['quantity'] += 1;
                                $item['subtotal'] = $item['price'] * $item['quantity'];
                            }
                            return $item;
                        });
                    } else {
                        $details->push([
                            'item_type' => $data['type'],
                            'item_id' => explode('-', $state)[1],
                            'name' => $data['label'],
                            'image' => $data['image'],
                            'price' => $data['price'],
                            'quantity' => 1,
                            'unit' => $data['type'] === 'product' ? 'pcs' : ($data['unit'] ?? 'unit'),
                            'subtotal' => $data['price'] * 1
                        ]);
                    }

                    $set('details', $details->toArray());

                    $total = $details->sum('subtotal');
                    $set('total', $total);
                }),

            Section::make('Detail Transaksi')
                ->schema([
                    Repeater::make('details')
                        ->schema([
                            TextInput::make('name')->label('Nama')->disabled(),
                            TextInput::make('unit')->label('Satuan')->disabled(),
                            TextInput::make('price')->label('Harga')->numeric()->disabled(),
                            TextInput::make('quantity')
                                ->label('Jumlah')
                                ->numeric()
                                ->reactive()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    $details = collect($get('details'));
                                    $index = $get('details')->search(fn($item) => $item['name'] === $get('name'));

                                    if ($index !== false) {
                                        $details[$index]['quantity'] = $state;
                                        $details[$index]['subtotal'] = $details[$index]['price'] * $state;
                                        $set('details', $details->toArray());

                                        $total = $details->sum('subtotal');
                                        $set('total', $total);
                                    }
                                }),

                            TextInput::make('subtotal')->label('Subtotal')->disabled()
                        ])
                        ->columns(6)
                        ->reorderable(false)
                        ->addable(false)
                        ->deletable(true)
                        ->default([]),
                ]),

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
                        ->default(0),

                    TextInput::make('paid_amount')
                        ->label('Uang Pembeli')
                        ->numeric()
                        ->required()
                        ->live()
                        ->afterStateUpdated(function ($state, $set, $get) {
                            $total = $get('total') ?? 0;
                            $set('change_amount', intval($state - $total));
                        }),

                    TextInput::make('change_amount')
                        ->label('Kembalian')
                        ->numeric()
                        ->disabled()
                        ->dehydrated()
                        ->default(0),
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
                        ->visible(fn(Forms\Get $get) => $get('add_new_customer') === false)
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
                        ->visible(fn(Forms\Get $get) => $get('add_new_customer') === true),
                    Hidden::make('customer_id'),
                ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')->label('Kode'),
                Tables\Columns\TextColumn::make('customer.name')->label('Customer'),
                Tables\Columns\TextColumn::make('total')->money('IDR')->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('paid_amount')->money('IDR')->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('change_amount')->money('IDR')->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.')),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->url(fn($record) => route('transactions.download.pdf', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('print_receipt')
                    ->label('Print Struk')
                    ->icon('heroicon-o-printer')
                    ->action(function ($record) {
                        // Logika cetak thermal
                        // Misal gunakan package seperti mike42/escpos-php
                        // Atau arahkan ke route eksternal (misal window.print dengan HTML khusus)

                        // Contoh redirect ke route untuk struk
                        return redirect()->route('transactions.print.receipt', $record);
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
