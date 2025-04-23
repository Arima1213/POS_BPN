<?php

namespace App\Filament\Resources\IncomeStatementResource\Pages;

use App\Filament\Resources\IncomeStatementResource;
use App\Models\JournalEntryDetail;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class IncomeStatemnt extends Page
{
    protected static string $resource = IncomeStatementResource::class;

    protected static string $view = 'income-statement.income-statement';

    public ?string $from = null;
    public ?string $until = null;

    public function getIncomeStatementData(): Collection
    {
        $details = JournalEntryDetail::query()
            ->with('akun')
            ->when($this->from && $this->until, function ($query) {
                $query->whereHas('journalEntry', function ($q) {
                    $q->whereBetween('tanggal', [$this->from, $this->until]);
                });
            })
            ->get();

        $pendapatan = $details->filter(fn($item) => str($item->akun->kode)->startsWith('4'))
            ->groupBy('akun.id')
            ->map(function ($items) {
                $akun = $items->first()->akun;
                $total = $items->sum(fn($i) => $i->kredit - $i->debit);
                return compact('akun', 'total');
            });

        $biaya = $details->filter(fn($item) => str($item->akun->kode)->startsWith('5'))
            ->groupBy('akun.id')
            ->map(function ($items) {
                $akun = $items->first()->akun;
                $total = $items->sum(fn($i) => $i->debit - $i->kredit);
                return compact('akun', 'total');
            });

        return collect([
            'pendapatan' => $pendapatan,
            'biaya' => $biaya,
        ]);
    }
}
