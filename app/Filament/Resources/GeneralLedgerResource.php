<?php

namespace App\Filament\Resources\GeneralLedgerResource\Pages;

use App\Filament\Resources\GeneralLedgerResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class ListGeneralLedgers extends Page
{
    protected static string $resource = JournalEntry::class;

    protected static string $view = 'filament.resources.general-ledger-resource.pages.general-ledger';

    public $from;
    public $until;

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->until = now()->endOfMonth()->toDateString();
    }

    public function getGeneralLedgerData()
    {
        $details = JournalEntryDetail::with(['jurnal', 'akun'])
            ->whereHas('jurnal', function ($query) {
                $query->whereBetween('tanggal', [$this->from, $this->until]);
            })
            ->orderBy('chart_of_account_id')
            ->orderBy('jurnal.tanggal')
            ->get()
            ->groupBy('chart_of_account_id');

        $ledger = [];

        foreach ($details as $akunId => $entries) {
            $akun = ChartOfAccount::find($akunId);
            $runningSaldo = 0;
            $rows = [];

            foreach ($entries as $entry) {
                $jumlah = $entry->jumlah;
                $debit = $entry->tipe === 'debit' ? $jumlah : 0;
                $kredit = $entry->tipe === 'kredit' ? $jumlah : 0;

                $runningSaldo += $debit;
                $runningSaldo -= $kredit;

                $rows[] = [
                    'tanggal' => $entry->jurnal->tanggal,
                    'transaksi' => $entry->jurnal->kategori,
                    'nomor' => $entry->jurnal->kode,
                    'keterangan' => $entry->deskripsi,
                    'debit' => $debit,
                    'kredit' => $kredit,
                    'saldo' => $runningSaldo,
                ];
            }

            $ledger[] = [
                'akun' => $akun,
                'rows' => $rows,
                'saldo_akhir' => $runningSaldo,
            ];
        }

        return $ledger;
    }
}
