<?php

namespace App\Filament\Owner\Resources\TransactionResource\Widgets;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Widgets\Widget;

class TotalPriveBulanIni extends Widget
{
    protected static string $view = 'filament.owner.resources.transaction-resource.widgets.total-prive-bulan-ini';

    public int|float $totalPrive = 0;

    public function mount(): void
    {
        $akunPrive = ChartOfAccount::where('kode', '3010')->first();

        if ($akunPrive) {
            $this->totalPrive = JournalEntryDetail::where('chart_of_account_id', $akunPrive->id)
                ->where('tipe', 'debit') // Penarikan prive
                ->whereHas('jurnal', function ($q) {
                    $q->whereBetween('tanggal', [
                        now()->startOfMonth()->toDateString(),
                        now()->endOfMonth()->toDateString(),
                    ]);
                })
                ->sum('jumlah');
        }
    }

    protected function getViewData(): array
    {
        return [
            'totalPrive' => $this->totalPrive,
        ];
    }
}
