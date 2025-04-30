<?php

namespace App\Filament\Owner\Resources\IncomeStatementResource\Widgets;

use App\Models\JournalEntryDetail;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class LabaRugiTren extends ChartWidget
{
    protected static ?string $heading = 'Laba / Rugi Bulanan (12 Bulan Terakhir)';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $labels = [];
        $pendapatan = [];
        $biaya = [];
        $labaRugi = [];

        for ($i = 11; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth()->toDateString();
            $end = now()->subMonths($i)->endOfMonth()->toDateString();
            $label = Carbon::parse($start)->translatedFormat('M Y');

            $labels[] = $label;

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

            $totalBiaya = JournalEntryDetail::whereHas(
                'akun',
                fn($q) =>
                $q->where('kelompok', 'beban')
            )
                ->whereHas(
                    'jurnal',
                    fn($q) =>
                    $q->whereBetween('tanggal', [$start, $end])
                )
                ->get()
                ->sum(fn($d) => $d->tipe === 'debit' ? $d->jumlah : -$d->jumlah);

            $pendapatan[] = $totalPendapatan;
            $biaya[] = $totalBiaya;
            $labaRugi[] = $totalPendapatan - $totalBiaya;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan',
                    'data' => $pendapatan,
                    'borderColor' => 'rgba(34, 197, 94, 1)', // green
                    'backgroundColor' => 'rgba(34, 197, 94, 0.2)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Biaya',
                    'data' => $biaya,
                    'borderColor' => 'rgba(239, 68, 68, 1)', // red
                    'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Laba/Rugi',
                    'data' => $labaRugi,
                    'borderColor' => 'rgba(59, 130, 246, 1)', // blue
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}