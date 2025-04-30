<?php

namespace App\Filament\Owner\Resources\WidgetForLifeResource\Widgets;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PendapatanBiayaPriveBulanIni extends BaseWidget
{
    protected function getStats(): array
    {
        $now = now();
        $labels = collect();
        $pendapatanChart = collect();
        $biayaChart = collect();
        $priveChart = collect();

        // Loop 6 bulan terakhir
        for ($i = 5; $i >= 0; $i--) {
            $start = $now->copy()->subMonths($i)->startOfMonth()->toDateString();
            $end = $now->copy()->subMonths($i)->endOfMonth()->toDateString();
            $labels->push($now->copy()->subMonths($i)->format('M'));

            $pendapatan = JournalEntryDetail::whereHas(
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

            $biaya = JournalEntryDetail::whereHas(
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

            $akunPrive = ChartOfAccount::where('kode', '3010')->first();
            $prive = $akunPrive ? JournalEntryDetail::where('chart_of_account_id', $akunPrive->id)
                ->where('tipe', 'debit')
                ->whereHas(
                    'jurnal',
                    fn($q) =>
                    $q->whereBetween('tanggal', [$start, $end])
                )
                ->sum('jumlah') : 0;

            $pendapatanChart->push($pendapatan);
            $biayaChart->push($biaya);
            $priveChart->push($prive);
        }

        // Data bulan ini dan bulan lalu
        $thisMonth = $pendapatanChart->last();
        $lastMonth = $pendapatanChart->count() >= 2 ? $pendapatanChart[$pendapatanChart->count() - 2] : 0;
        $diff = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;
        $icon = $diff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
        $color = $diff >= 0 ? 'success' : 'danger';

        return [
            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($thisMonth, 0, ',', '.'))
                ->description(round($diff, 1) . '% dari bulan lalu')
                ->descriptionIcon($icon, IconPosition::Before)
                ->chart($pendapatanChart->toArray())
                ->color($color),

            Stat::make('Biaya Operasional', 'Rp ' . number_format($biayaChart->last(), 0, ',', '.'))
                ->description('Biaya 6 bulan terakhir')
                ->chart($biayaChart->toArray())
                ->color('warning'),

            Stat::make('Prive Bulan Ini', 'Rp ' . number_format($priveChart->last(), 0, ',', '.'))
                ->description('Total Prive')
                ->chart($priveChart->toArray())
                ->color('danger'),
        ];
    }
}