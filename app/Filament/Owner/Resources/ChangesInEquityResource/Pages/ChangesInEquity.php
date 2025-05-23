<?php

namespace App\Filament\Owner\Resources\ChangesInEquityResource\Pages;

use App\Filament\Owner\Resources\ChangesInEquityResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\Page;

class ChangesInEquity extends Page
{
    protected static string $resource = ChangesInEquityResource::class;
    protected static ?string $title = 'Perubahan Equitas';
    protected static ?string $navigationLabel = 'Perubahan Equitas';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

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
        $akunModal = ChartOfAccount::where('kode', '3000')->first();
        $akunPrive = ChartOfAccount::where('kode', '3010')->first();

        $modalAwal = $this->getSaldoAkun($akunModal?->id, '<', $this->from);
        $penambahanModal = $this->getMutasiModal($akunModal?->id); // Mutasi selama periode
        $totalPrive = $this->getTotalPrive($akunPrive?->id);
        $labaBersih = $this->getLabaBersih();

        $modalAkhir = $modalAwal + $penambahanModal + $labaBersih - $totalPrive;

        return [
            'modal_awal' => $modalAwal,
            'penambahan_modal' => $penambahanModal,
            'laba_bersih' => $labaBersih,
            'prive' => $totalPrive,
            'modal_akhir' => $modalAkhir,
        ];
    }

    protected function getSaldoAkun($akunId, $operator = '<', $date = null)
    {
        if (!$akunId) return 0;

        return JournalEntryDetail::where('chart_of_account_id', $akunId)
            ->whereHas('jurnal', fn($q) => $q->where('tanggal', $operator, $date ?? $this->from))
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? -$d->jumlah : $d->jumlah);
    }

    protected function getMutasiModal($akunId)
    {
        if (!$akunId) return 0;

        return JournalEntryDetail::where('chart_of_account_id', $akunId)
            ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$this->from, $this->until]))
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? -$d->jumlah : $d->jumlah);
    }

    protected function getTotalPrive($akunId)
    {
        if (!$akunId) return 0;

        // Karena prive normalnya bertipe debit, kita ambil semua yang bertipe debit (penarikan prive)
        return JournalEntryDetail::where('chart_of_account_id', $akunId)
            ->where('tipe', 'debit')
            ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$this->from, $this->until]))
            ->sum('jumlah');
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
