<?php

namespace App\Filament\Exports;

use App\Models\Transactions;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Facades\DB;

class TransactionLogOwnerExporter extends Exporter
{
    public static function query()
    {
        // Execute any necessary logic before exporting data
        self::executePreExportLogic();

        return Transactions::query()
            ->select([
                DB::raw('MIN(id) as id'),
                DB::raw('DATE(created_at) as transaction_date'),
                DB::raw('SUM(total) as total_sales'),
                DB::raw('SUM(paid_amount) as total_paid')
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc(DB::raw('DATE(created_at)'));
    }

    protected static function executePreExportLogic()
    {
        // Add any pre-export logic here, such as data preparation or validation
        // Example: Log a message or perform a specific action
        logger()->info('Executing pre-export logic for TransactionLogOwnerExporter.');
    }

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('transaction_date')
                ->label('Tanggal')
                ->stateUsing(fn($record) => $record->transaction_date),
            ExportColumn::make('total_sales')
                ->label('Total Penjualan')
                ->stateUsing(fn($record) => $record->total_sales)
                ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
            ExportColumn::make('total_paid')
                ->label('Total Dibayar')
                ->stateUsing(fn($record) => $record->total_paid)
                ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
            ExportColumn::make('outstanding')
                ->label('Total Belum Dibayar')
                ->stateUsing(fn($record) => $record->total_sales - $record->total_paid)
                ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.')),
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