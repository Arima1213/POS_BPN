<?php

namespace App\Filament\Exports;

use App\Models\Transactions;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TransactionLogOwnerExporter extends Exporter
{
    protected static ?string $model = Transactions::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('transaction_date')->label('Tanggal'),
            ExportColumn::make('total_sales')->label('Total Penjualan'),
            ExportColumn::make('total_paid')->label('Total Dibayar'),
            ExportColumn::make('outstanding')->label('Total Belum Dibayar')
                ->state(function ($record) {
                    return $record->total_sales - $record->total_paid;
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Export transaksi harian Anda telah selesai. Total ' . number_format($export->successful_rows) . ' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' baris gagal diekspor.';
        }

        return $body;
    }
}