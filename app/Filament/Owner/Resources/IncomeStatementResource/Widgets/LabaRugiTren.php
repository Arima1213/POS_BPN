<?php

namespace App\Filament\Owner\Resources\IncomeStatementResource\Widgets;

use App\Models\JournalEntryDetail;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class LabaRugiTren extends ChartWidget
{
    protected static ?string $heading = 'Laba / Rugi Bulanan (12 Bulan Terakhir)';
    protected static ?int $sort = 4;

    protected static ?string $from = null;
    protected static ?string $until = null;

    public static function setFilters(string $from, string $until): void
    {
        static::$from = $from;
        static::$until = $until;
    }

    protected function getData(): array
    {
        $labels = [];
        $pendapatan = [];
        $biaya = [];
        $labaRugi = [];

        $from = static::$from ? Carbon::parse(static::$from)->startOfMonth() : now()->subMonths(11)->startOfMonth();
        $until = static::$until ? Carbon::parse(static::$until)->endOfMonth() : now()->endOfMonth();

        $period = [];
        $current = $from->copy();
        while ($current->lessThanOrEqualTo($until)) {
            $period[] = $current->copy();
            $current->addMonth();
        }

        foreach ($period as $date) {
            $start = $date->copy()->startOfMonth()->toDateString();
            $end = $date->copy()->endOfMonth()->toDateString();
            $label = $date->translatedFormat('M Y');

            $labels[] = $label;

            $totalPendapatan = JournalEntryDetail::whereHas(
                'akun',
                fn($q) => $q->where('kelompok', 'pendapatan')
            )
                ->whereHas(
                    'jurnal',
                    fn($q) => $q->whereBetween('tanggal', [$start, $end])
                )
                ->get()
                ->sum(fn($d) => $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah);

            $totalBiaya = JournalEntryDetail::whereHas(
                'akun',
                fn($q) => $q->where('kelompok', 'beban')
            )
                ->whereHas(
                    'jurnal',
                    fn($q) => $q->whereBetween('tanggal', [$start, $end])
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
