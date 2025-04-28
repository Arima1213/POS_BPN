<?php

namespace App\Filament\Resources\IncomeStatementResource\Pages;

use App\Filament\Resources\IncomeStatementResource;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Collection;
use Livewire\WithPagination;

class IncomeStatement extends Page
{
    use WithPagination;

    protected static string $resource = IncomeStatementResource::class;

    protected static string $view = 'income-statement.income-statement';

    public ?string $from = null;
    public ?string $until = null;

    public Collection $pendapatan;
    public Collection $biaya;

    public float $totalPendapatan = 0;
    public float $totalBiaya = 0;
    public float $labaBersih = 0;

    public function mount(): void
    {
        // Default tanggal ke awal bulan ini
        $this->from = now()->startOfMonth()->toDateString();
        $this->until = now()->endOfMonth()->toDateString();

        // Inisialisasi data kosong dulu
        $this->pendapatan = collect();
        $this->biaya = collect();
    }

    public function tampilkan(): void
    {
        $details = JournalEntryDetail::query()
            ->with(['akun', 'jurnal'])
            ->whereHas('jurnal', function ($q) {
                $q->whereBetween('tanggal', [$this->from, $this->until]);
            })
            ->get();

        $this->pendapatan = $details->filter(fn($item) => str($item->akun->kode)->startsWith('4'))
            ->groupBy('akun.id')
            ->map(function ($items) {
                $akun = $items->first()->akun;
                $total = $items->sum(function ($i) {
                    return $i->tipe === 'kredit' ? $i->jumlah : -$i->jumlah;
                });
                return compact('akun', 'total');
            });

        $this->biaya = $details->filter(fn($item) => str($item->akun->kode)->startsWith('5'))
            ->groupBy('akun.id')
            ->map(function ($items) {
                $akun = $items->first()->akun;
                $total = $items->sum(function ($i) {
                    return $i->tipe === 'debit' ? $i->jumlah : -$i->jumlah;
                });
                return compact('akun', 'total');
            });

        $this->totalPendapatan = $this->pendapatan->sum('total');
        $this->totalBiaya = $this->biaya->sum('total');
        $this->labaBersih = $this->totalPendapatan - $this->totalBiaya;
    }
}
