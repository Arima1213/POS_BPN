<?php

namespace App\Filament\Owner\Resources\GeneralLedgerResource\Pages;

use App\Filament\Owner\Resources\GeneralLedgerResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\Page;

class GeneralLedger extends Page
{
    protected static string $resource = GeneralLedgerResource::class;
    protected static ?string $title = 'Buku Besar';
    protected static ?string $navigationLabel = 'Buku Besar';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'buku-besar';
    protected static ?string $label = 'Buku Besar';
    protected static ?string $pluralLabel = 'Buku Besar';


    protected static string $view = 'general-ledger.general-ledger';

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
            ->get()
            ->sortBy(function ($detail) {
                return $detail->jurnal->tanggal;
            })
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
