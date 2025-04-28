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

    public $reportData = [];

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->until = now()->endOfMonth()->toDateString();
        $this->loadReport();
    }

    public function loadReport(): void
    {
        $this->reportData = $this->getEquityReport();
    }

    public function getEquityReport(): array
    {
        $akunModal = ChartOfAccount::where('nama', 'Modal')->first();
        $akunPrive = ChartOfAccount::where('nama', 'Prive')->first();

        $modalAwal = $this->getSaldoAkun($akunModal?->id, '<', $this->from);
        $totalPrive = $this->getMutasi($akunPrive?->id);
        $labaBersih = $this->getLabaBersih();

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

    protected function getLabaBersih(): float
    {
        $pendapatan = JournalEntryDetail::whereHas('akun', function ($q) {
            $q->where('kelompok', 'pendapatan');
        })
            ->whereHas('jurnal', function ($q) {
                $q->whereBetween('tanggal', [$this->from, $this->until]);
            })
            ->get()
            ->sum(fn($d) => $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah);

        $beban = JournalEntryDetail::whereHas('akun', function ($q) {
            $q->where('kelompok', 'beban');
        })
            ->whereHas('jurnal', function ($q) {
                $q->whereBetween('tanggal', [$this->from, $this->until]);
            })
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? $d->jumlah : -$d->jumlah);

        return $pendapatan - $beban;
    }
}
