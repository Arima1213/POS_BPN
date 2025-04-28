<?php

namespace App\Filament\Exports;

use App\Models\ChartOfAccount;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ChartOfAccountExporter extends Exporter
{
    protected static ?string $model = ChartOfAccount::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('kode'),
            ExportColumn::make('nama'),
            ExportColumn::make('kelompok'),
            ExportColumn::make('tipe'),
            ExportColumn::make('jenis_beban'),
            ExportColumn::make('deskripsi'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your chart of account export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
