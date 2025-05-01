<?php

namespace App\Filament\Owner\Resources\WidgetForLifeResource\Widgets;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Widgets\ChartWidget;

class ModalVsPrive extends ChartWidget
{
    protected static ?string $heading = 'Modal vs Prive Bulan Ini';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $from = now()->startOfMonth()->toDateString();
        $until = now()->endOfMonth()->toDateString();

        $akunModal = ChartOfAccount::where('kode', '3000')->first();
        $akunPrive = ChartOfAccount::where('kode', '3010')->first();

        $penambahanModal = 0;
        $totalPrive = 0;

        if ($akunModal) {
            $penambahanModal = JournalEntryDetail::where('chart_of_account_id', $akunModal->id)
                ->whereHas('jurnal', function ($q) use ($from, $until) {
                    $q->whereBetween('tanggal', [$from, $until]);
                })
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
            'labels' => ['Penambahan Modal', 'Prive'],
            'datasets' => [
                [
                    'label' => 'Jumlah (Rp)',
                    'data' => [$penambahanModal, $totalPrive],
                    'backgroundColor' => ['#3b82f6', '#ef4444'], // biru dan merah
                ],
            ],
        ];
    }
}
