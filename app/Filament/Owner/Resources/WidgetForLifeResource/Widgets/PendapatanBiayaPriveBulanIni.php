<?php

namespace App\Filament\Owner\Resources\WidgetForLifeResource\Widgets;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendapatanBiayaPriveBulanIni extends BaseWidget
{
    protected function getStats(): array
    {
        $start = now()->startOfMonth()->toDateString();
        $end = now()->endOfMonth()->toDateString();

        // Total Pendapatan Bulan Ini
        $totalPendapatan = JournalEntryDetail::whereHas(
            'akun',
            fn($q) =>
            $q->where('kelompok', 'pendapatan')
        )
            ->whereHas(
                'jurnal',
                fn($q) =>
                $q->whereBetween('tanggal', [$start, $end])
            )
            ->get()
            ->sum(fn($d) => $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah);

        // Total Biaya Operasional Bulan Ini
        $totalBiaya = JournalEntryDetail::whereHas(
            'akun',
            fn($q) =>
            $q->where('kelompok', 'beban')->where('tipe', 'operasional')
        )
            ->whereHas(
                'jurnal',
                fn($q) =>
                $q->whereBetween('tanggal', [$start, $end])
            )
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? $d->jumlah : -$d->jumlah);

        // Total Prive Bulan Ini
        $akunPrive = ChartOfAccount::where('kode', '3010')->first();
        $totalPrive = 0;
        if ($akunPrive) {
            $totalPrive = JournalEntryDetail::where('chart_of_account_id', $akunPrive->id)
                ->where('tipe', 'debit')
                ->whereHas(
                    'jurnal',
                    fn($q) =>
                    $q->whereBetween('tanggal', [$start, $end])
                )
                ->sum('jumlah');
        }

        return [
            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($totalPendapatan, 0, ',', '.'))
                ->description('Total Pendapatan')
                ->color('success'),

            Stat::make('Biaya Operasional Bulan Ini', 'Rp ' . number_format($totalBiaya, 0, ',', '.'))
                ->description('Total Biaya Operasional')
                ->color('danger'),

            Stat::make('Prive Bulan Ini', 'Rp ' . number_format($totalPrive, 0, ',', '.'))
                ->description('Total Prive')
                ->color('warning'),
        ];
    }
}
