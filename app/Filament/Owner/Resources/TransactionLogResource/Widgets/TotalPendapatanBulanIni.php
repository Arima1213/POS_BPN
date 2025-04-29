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
        $this->totalPendapatan = JournalEntryDetail::whereHas('akun', function ($query) {
            $query->where('kelompok', 'pendapatan');
        })
            ->whereHas('jurnal', function ($query) {
                $query->whereBetween('tanggal', [
                    now()->startOfMonth()->toDateString(),
                    now()->endOfMonth()->toDateString()
                ]);
            })
            ->sum(fn($d) => $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah);
    }
}
