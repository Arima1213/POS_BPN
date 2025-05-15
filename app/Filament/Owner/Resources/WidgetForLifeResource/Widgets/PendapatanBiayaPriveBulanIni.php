<?php

namespace App\Filament\Owner\Resources\WidgetForLifeResource\Widgets;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PendapatanBiayaPriveBulanIni extends BaseWidget
{
    protected static ?string $from = null;
    protected static ?string $until = null;

    public static function setFilters(string $from, string $until): void
    {
        static::$from = $from;
        static::$until = $until;
    }

    protected function getStats(): array
    {
        // Ambil range tanggal dari filter, default ke bulan ini jika tidak ada
        $start = static::$from ? Carbon::parse(static::$from)->startOfDay() : now()->startOfMonth();
        $end = static::$until ? Carbon::parse(static::$until)->endOfDay() : now()->endOfMonth();

        // Hitung nilai untuk range filter (untuk stat utama)
        $pendapatan = JournalEntryDetail::whereHas('akun', fn($q) => $q->where('kelompok', 'pendapatan'))
            ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$start, $end]))
            ->get()
            ->sum(fn($d) => $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah);

        $biaya = JournalEntryDetail::whereHas('akun', fn($q) => $q->where('kelompok', 'beban')->where('tipe', 'operasional'))
            ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$start, $end]))
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? $d->jumlah : -$d->jumlah);

        $akunPrive = ChartOfAccount::where('kode', '3010')->first();
        $prive = $akunPrive ? JournalEntryDetail::where('chart_of_account_id', $akunPrive->id)
            ->where('tipe', 'debit')
            ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$start, $end]))
            ->sum('jumlah') : 0;

        // Data chart 6 bulan terakhir
        $now = now();
        $pendapatanChart = collect();
        $biayaChart = collect();
        $priveChart = collect();

        for ($i = 5; $i >= 0; $i--) {
            $chartStart = $now->copy()->subMonths($i)->startOfMonth();
            $chartEnd = $now->copy()->subMonths($i)->endOfMonth();

            $pendapatanChart->push(
                JournalEntryDetail::whereHas('akun', fn($q) => $q->where('kelompok', 'pendapatan'))
                    ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$chartStart, $chartEnd]))
                    ->get()
                    ->sum(fn($d) => $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah)
            );

            $biayaChart->push(
                JournalEntryDetail::whereHas('akun', fn($q) => $q->where('kelompok', 'beban')->where('tipe', 'operasional'))
                    ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$chartStart, $chartEnd]))
                    ->get()
                    ->sum(fn($d) => $d->tipe === 'debit' ? $d->jumlah : -$d->jumlah)
            );

            $priveChart->push(
                $akunPrive ? JournalEntryDetail::where('chart_of_account_id', $akunPrive->id)
                    ->where('tipe', 'debit')
                    ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$chartStart, $chartEnd]))
                    ->sum('jumlah') : 0
            );
        }

        return [
            Stat::make('Pendapatan', 'Rp ' . number_format($pendapatan, 0, ',', '.'))
                ->chart($pendapatanChart->toArray())
                ->chartColor('primary'),

            Stat::make('Biaya Operasional', 'Rp ' . number_format($biaya, 0, ',', '.'))
                ->chart($biayaChart->toArray())
                ->chartColor('warning'),

            Stat::make('Prive', 'Rp ' . number_format($prive, 0, ',', '.'))
                ->chart($priveChart->toArray())
                ->chartColor('danger'),
        ];
    }
}