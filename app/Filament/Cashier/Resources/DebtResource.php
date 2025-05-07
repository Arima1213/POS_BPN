<?php

namespace App\Filament\Cashier\Resources;

use App\Filament\Cashier\Resources\DebtResource\Pages;
use App\Models\ChartOfAccount;
use App\Models\Debt;
use Illuminate\Support\Str;
use App\Models\DebtPayment;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Grid;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Illuminate\Support\HtmlString;

use Filament\Notifications\Notification;

class DebtResource extends Resource
{
    protected static ?string $model = Debt::class;

    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $pluralLabel = 'Kelola Transaksi';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationSort(): int
    {
        return 2;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining')
                    ->label('Remaining Payment')
                    ->money('IDR')
                    ->getStateUsing(fn(Debt $record) => max(0, $record->amount - $record->paid))
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid')
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->label('Due Date')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Created At')
                    ->sortable(),
            ])
            ->actions([
                Action::make('bayar')
                    ->label('Bayar')
                    ->icon('heroicon-m-banknotes')
                    ->form(fn(Debt $record) => [
                        Hidden::make('debt_id')->default($record->id),

                        TextInput::make('amount')
                            ->label('Nominal Pembayaran')
                            ->numeric()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) use ($record) {
                                if ($state > ($record->amount - $record->paid)) {
                                    $set('amount', $record->amount - $record->paid);
                                }
                            }),

                        DatePicker::make('payment_date')
                            ->label('Tanggal Pembayaran')
                            ->required(),
                    ])
                    ->action(function (array $data, Debt $record): void {
                        DebtPayment::create($data);
                        $record->paid += $data['amount'];
                        $record->save();

                        // Buat entri jurnal utama
                        $journal = JournalEntry::create([
                            'tanggal' => now(),
                            'kode' => 'JE-' . strtoupper(Str::random(6)),
                            'keterangan' => 'Pembayaran Piutang: ' . $record->id,
                            'kategori' => 'aset',
                        ]);

                        // Debit: Pembayaran Piutang (kode akun 1022)
                        JournalEntryDetail::create([
                            'journal_entry_id' => $journal->id,
                            'chart_of_account_id' => ChartOfAccount::where('kode', '1022')->value('id'),
                            'tipe' => 'debit',
                            'jumlah' => $data['amount'],
                            'deskripsi' => 'Pembayaran piutang untuk Debt ID ' . $record->id,
                        ]);

                        // Kredit: Kas Kecil (kode akun 1000)
                        JournalEntryDetail::create([
                            'journal_entry_id' => $journal->id,
                            'chart_of_account_id' => ChartOfAccount::where('kode', '1000')->value('id'),
                            'tipe' => 'kredit',
                            'jumlah' => $data['amount'],
                            'deskripsi' => 'Pemasukan kas oleh pembayaran piutang Debt ID ' . $record->id,
                        ]);

                        Notification::make()
                            ->title('Pembayaran berhasil')
                            ->success()
                            ->send();
                    })
                    ->disabled(fn(Debt $record) => $record->paid >= $record->amount)
                    ->modalHeading('Form Pembayaran Hutang'),

                Action::make('riwayat')
                    ->color('white')
                    ->label('Riwayat')
                    ->icon('heroicon-m-eye')
                    ->modalHeading('Riwayat Cicilan')
                    ->modalContent(function (Debt $record) {
                        $payments = $record->payments()->orderBy('payment_date', 'desc')->get();

                        if ($payments->isEmpty()) {
                            return new HtmlString('<p>Belum ada cicilan.</p>');
                        }

                        $html = '<ul class="space-y-2">';
                        foreach ($payments as $payment) {
                            $tanggal = \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y');
                            $html .= "<li><strong>{$tanggal}:</strong> Rp " . number_format($payment->amount, 0, ',', '.') . '</li>';
                        }
                        $html .= '</ul>';

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                Action::make('detail_pelanggan')
                    ->label('Pelanggan')
                    ->icon('heroicon-m-user-circle')
                    ->color('info')
                    ->modalHeading('Detail Pelanggan')
                    ->modalContent(function (Debt $record) {
                        $customer = $record->customer;

                        if (!$customer) {
                            return new HtmlString('<p>Data pelanggan tidak tersedia.</p>');
                        }

                        $html = '<div class="space-y-2">';
                        $html .= "<p><strong>Nama:</strong> {$customer->name}</p>";
                        $html .= "<p><strong>Email:</strong> {$customer->email}</p>";
                        $html .= "<p><strong>No. Telepon:</strong> {$customer->phone}</p>";
                        $html .= "<p><strong>Alamat:</strong> {$customer->address}</p>";
                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),


            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDebts::route('/'),
        ];
    }
}