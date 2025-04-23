<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyReceivablesResource\Pages;
use App\Models\Debt;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Support\HtmlString;

class CompanyReceivablesResource extends Resource
{
    protected static ?string $model = Debt::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Piutang Perusahaan';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining')
                    ->label('Sisa Piutang')
                    ->money('IDR')
                    ->getStateUsing(fn(Debt $record) => max(0, $record->amount - $record->paid))
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Total Piutang')
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('paid')
                    ->label('Telah Dibayar')
                    ->formatStateUsing(fn($state) => 'IDR ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (Debt $record) {
                        return ($record->amount - $record->paid) > 0 ? 'Belum Lunas' : 'Lunas';
                    })
                    ->colors([
                        'danger' => 'Belum Lunas',
                        'success' => 'Lunas',
                    ]),
            ])
            ->actions([
                Action::make('riwayat')
                    ->color('gray')
                    ->label('Riwayat')
                    ->icon('heroicon-m-eye')
                    ->modalHeading('Riwayat Pembayaran')
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
                    ->label('Detail')
                    ->icon('heroicon-m-user-circle')
                    ->color('info')
                    ->modalHeading('Data Pelanggan')
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
            'index' => Pages\ListCompanyReceivables::route('/'),
        ];
    }
}
