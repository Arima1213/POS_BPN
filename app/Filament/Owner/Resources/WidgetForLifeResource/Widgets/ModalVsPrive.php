<?php

namespace App\Filament\Owner\Resources\WidgetForLifeResource\Widgets;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Widgets\ChartWidget;

class ModalVsPrive extends ChartWidget
{
    protected static ?string $heading = 'Modal vs Prive (Bulan Ini)';
    protected static ?string $from = null;
    protected static ?string $until = null;

    public static function setFilters(string $from, string $until): void
    {
        static::$from = $from;
        static::$until = $until;
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $from = static::$from ?? now()->startOfMonth()->toDateString();
        $until = static::$until ?? now()->endOfMonth()->toDateString();

        $akunModal = ChartOfAccount::where('kode', '3000')->first();
        $akunPrive = ChartOfAccount::where('kode', '3010')->first();

        $penambahanModal = 0;
        $totalPrive = 0;

        if ($akunModal) {
            $penambahanModal = JournalEntryDetail::where('chart_of_account_id', $akunModal->id)
                ->whereHas('jurnal', function ($q) use ($from, $until) {
                    $q->whereBetween('tanggal', [$from, $until]);
                })
                ->get()
                ->sum(fn($d) => $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah);
        }

        if ($akunPrive) {
            $totalPrive = JournalEntryDetail::where('chart_of_account_id', $akunPrive->id)
                ->where('tipe', 'debit')
                ->whereHas('jurnal', function ($q) use ($from, $until) {
                    $q->whereBetween('tanggal', [$from, $until]);
                })
                ->sum('jumlah');
        }

        return [
            'labels' => ['Modal Masuk', 'Prive Keluar'],
            'datasets' => [
                [
                    'label' => 'Jumlah (Rp)',
                    'data' => [$penambahanModal, $totalPrive],
                    'backgroundColor' => ['#3b82f6', '#ef4444'],
                    'borderRadius' => 8,
                    'barThickness' => 50,
                ],
            ],
        ];
    }
}