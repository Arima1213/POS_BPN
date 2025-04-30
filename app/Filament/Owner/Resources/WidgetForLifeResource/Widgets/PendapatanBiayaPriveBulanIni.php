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
        $pendapatanChart = collect();
        $biayaChart = collect();
        $priveChart = collect();

        for ($i = 5; $i >= 0; $i--) {
            $start = $now->copy()->subMonths($i)->startOfMonth();
            $end = $now->copy()->subMonths($i)->endOfMonth();

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

        // Helper fungsi
        $calcChange = function ($data) {
            $thisMonth = $data->last();
            $lastMonth = $data->count() >= 2 ? $data[$data->count() - 2] : 0;
            $diff = $lastMonth > 0
                ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1)
                : ($thisMonth > 0 ? 100 : 0); // anggap 100% naik dari 0 ke nilai

            $icon = $diff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
            $color = $diff >= 0 ? 'success' : 'danger';
            return [
                'value' => $thisMonth,
                'diff' => $diff,
                'icon' => $icon,
                'color' => $color,
                'last' => $lastMonth,
            ];
        };

        $pendapatan = $calcChange($pendapatanChart);
        $biaya = $calcChange($biayaChart);
        $prive = $calcChange($priveChart);

        return [
            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatan['value'], 0, ',', '.'))
                ->description(number_format($pendapatan['diff'], 1) . '% dari bulan lalu')
                ->descriptionIcon($pendapatan['icon'], IconPosition::Before)
                ->chart($pendapatanChart->toArray())
                ->chartColor('primary') // Warna chart untuk pendapatan
                ->color($pendapatan['color']),

            Stat::make('Biaya Operasional', 'Rp ' . number_format($biaya['value'], 0, ',', '.'))
                ->description(number_format($biaya['diff'], 1) . '% dari bulan lalu')
                ->descriptionIcon($biaya['icon'], IconPosition::Before)
                ->chart($biayaChart->toArray())
                ->chartColor('warning') // Warna chart untuk biaya
                ->color($biaya['color']),

            Stat::make('Prive Bulan Ini', 'Rp ' . number_format($prive['value'], 0, ',', '.'))
                ->description(number_format($prive['diff'], 1) . '% dari bulan lalu')
                ->descriptionIcon($prive['icon'], IconPosition::Before)
                ->chart($priveChart->toArray())
                ->chartColor('danger') // Warna chart untuk prive
                ->color($prive['color']),
        ];
    }
}