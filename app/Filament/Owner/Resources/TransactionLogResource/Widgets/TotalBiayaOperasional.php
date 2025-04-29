<?php

namespace App\Filament\Owner\Resources\TransactionLogResource\Widgets;

use App\Models\JournalEntryDetail;
use Filament\Widgets\Widget;

class TotalBiayaOperasional extends Widget
{
    protected static string $view = 'filament.owner.resources.transaction-log-resource.widgets.total-biaya-operasional';

    public int|float $totalBiaya = 0;

    public function mount(): void
    {
        $entries = JournalEntryDetail::whereHas('akun', function ($query) {
            $query->where('kelompok', 'beban')
                ->where('tipe', 'operasional');
        })
            ->whereHas('jurnal', function ($query) {
                $query->whereBetween('tanggal', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString()
                ]);
            })
            ->get();

        $this->totalBiaya = $entries->sum(function ($entry) {
            return $entry->tipe === 'debit' ? $entry->jumlah : -$entry->jumlah;
        });
    }

    protected function getViewData(): array
    {
        return [
            'totalBiaya' => $this->totalBiaya,
        ];
    }
}