<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaxResource\Pages;
use App\Filament\Resources\TaxResource\RelationManagers;
use App\Models\Tax;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Pajak';
    protected static ?string $navigationGroup = 'Keuangan';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tax_type')
                    ->label('Jenis Pajak')
                    ->helperText('Pilih jenis pajak yang ingin dihitung.')
                    ->options([
                        'PPh 21' => 'PPh 21 (Penghasilan)',
                        'PPN' => 'PPN (Pertambahan Nilai)',
                        'PBB' => 'PBB (Bumi dan Bangunan)',
                    ])
                    ->required(),

                DatePicker::make('tax_period')
                    ->label('Periode Pajak')
                    ->displayFormat('Y-m')
                    ->helperText('Pilih periode pajak dalam format tahun-bulan.')
                    ->required(),

                TextInput::make('npwp')
                    ->label('NPWP (Opsional)')
                    ->placeholder('99.999.999.9-999.999')
                    ->helperText('Masukkan NPWP jika ada.'),

                TextInput::make('amount_due')
                    ->label('Jumlah Pajak Terutang')
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Masukkan jumlah total pajak yang terutang (hasil perhitungan otomatis).')
                    ->reactive()
                    ->required()
                    ->afterStateUpdated(fn($state, callable $set) => self::updateStatus($set, $state)),

                TextInput::make('amount_paid')
                    ->label('Jumlah yang Dibayar')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->reactive()
                    ->helperText('Masukkan jumlah pajak yang sudah dibayarkan.')
                    ->afterStateUpdated(fn($state, callable $set) => self::updateStatus($set, null, $state)),

                Select::make('status')
                    ->label('Status Pembayaran')
                    ->disabled()
                    ->options([
                        'Belum Dibayar' => 'Belum Dibayar',
                        'Sebagian Dibayar' => 'Sebagian Dibayar',
                        'Lunas' => 'Lunas',
                        'Nunggak' => 'Nunggak',
                    ]),

                DatePicker::make('due_date')
                    ->label('Tanggal Jatuh Tempo')
                    ->helperText('Masukkan tanggal jatuh tempo pembayaran pajak.'),

                Textarea::make('description')
                    ->label('Catatan')
                    ->rows(2)
                    ->placeholder('Masukkan catatan tambahan jika ada.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tax_type')->label('Jenis Pajak')->sortable(),
                Tables\Columns\TextColumn::make('tax_period')->label('Periode'),
                Tables\Columns\TextColumn::make('amount_due')->money('IDR')->label('Jumlah Terutang'),
                Tables\Columns\TextColumn::make('amount_paid')->money('IDR')->label('Dibayar'),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function updateStatus(callable $set, $amountDue = null, $amountPaid = null)
    {
        $amountDue = $amountDue ?? request()->input('data.amount_due') ?? 0;
        $amountPaid = $amountPaid ?? request()->input('data.amount_paid') ?? 0;

        if ($amountPaid == 0) {
            $set('status', 'Belum Dibayar');
        } elseif ($amountPaid < $amountDue) {
            $set('status', 'Sebagian Dibayar');
        } elseif ($amountPaid >= $amountDue) {
            $set('status', 'Lunas');
        }
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
            'index' => Pages\ListTaxes::route('/'),
            'create' => Pages\CreateTax::route('/create'),
            'edit' => Pages\EditTax::route('/{record}/edit'),
        ];
    }
}