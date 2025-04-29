<?php

namespace App\Filament\Owner\Resources\TransactionLogResource\Widgets;

use App\Models\JournalEntryDetail;
use Filament\Widgets\Widget;

class TotalPendapatanBulanIni extends Widget
{
    protected static string $view = 'filament.owner.resources.transaction-log-resource.widgets.total-pendapatan-bulan-ini';
    public int $totalPendapatan = 0;

    public function mount(): void
    {
        $entries = JournalEntryDetail::whereHas('akun', function ($query) {
            $query->where('kelompok', 'pendapatan');
        })
            ->whereHas('jurnal', function ($query) {
                $query->whereBetween('tanggal', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString()
                ]);
            })
            ->get(); // penting: ambil semua data dulu

        $this->totalPendapatan = $entries->sum(function ($d) {
            return $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah;
        });
    }
}
