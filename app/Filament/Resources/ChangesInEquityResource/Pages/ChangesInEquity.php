<?php

namespace App\Filament\Resources\ChangesInEquityResource\Pages;

use App\Filament\Resources\ChangesInEquityResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\Page;

class ChangesInEquity extends Page
{
    protected static string $resource = ChangesInEquityResource::class;

    protected static string $view = 'changes-in-equity.changes-in-equity';

    public $from;
    public $until;

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->until = now()->endOfMonth()->toDateString();
    }

    public function getEquityReport()
    {
        // Ambil akun modal, prive, dan laba ditahan
        $akunModal = ChartOfAccount::where('nama', 'Modal')->first();
        $akunPrive = ChartOfAccount::where('nama', 'Prive')->first();
        $akunLaba = ChartOfAccount::where('nama', 'Laba Ditahan')->first();

        $modalAwal = $this->getSaldoAkun($akunModal?->id, '<', $this->from);
        $totalPrive = $this->getMutasi($akunPrive?->id);
        $labaBersih = $this->getMutasi($akunLaba?->id);

        $modalAkhir = $modalAwal + $labaBersih - $totalPrive;

        return [
            'modal_awal' => $modalAwal,
            'laba_bersih' => $labaBersih,
            'prive' => $totalPrive,
            'modal_akhir' => $modalAkhir,
        ];
    }

    protected function getMutasi($akunId)
    {
        if (!$akunId) return 0;

        return JournalEntryDetail::where('chart_of_account_id', $akunId)
            ->whereHas('jurnal', function ($q) {
                $q->whereBetween('tanggal', [$this->from, $this->until]);
            })
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? -$d->jumlah : $d->jumlah);
    }

    protected function getSaldoAkun($akunId, $operator = '<', $date = null)
    {
        if (!$akunId) return 0;

        return JournalEntryDetail::where('chart_of_account_id', $akunId)
            ->whereHas('jurnal', fn($q) => $q->where('tanggal', $operator, $date ?? $this->from))
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? -$d->jumlah : $d->jumlah);
    }
}
