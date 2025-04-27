<?php

namespace App\Filament\Exports;

use App\Models\Transactions;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionLogExporter extends Exporter
{
    protected static ?string $model = Transactions::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code')->label('Kode Transaksi'),
            ExportColumn::make('customer.name')->label('Nama Customer'),
            ExportColumn::make('total')->label('Total'),
            ExportColumn::make('paid_amount')->label('Dibayar'),
            ExportColumn::make('change_amount')->label('Kembalian'),
            ExportColumn::make('created_at')->label('Tanggal'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export transaksi Anda telah selesai. Total ' . number_format($export->successful_rows) . ' ' . str('baris')->plural($export->successful_rows) . ' berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diekspor.';
        }

        return $body;
    }
}
