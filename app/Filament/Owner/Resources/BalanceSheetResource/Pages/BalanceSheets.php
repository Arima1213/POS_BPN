<?php

namespace App\Filament\Owner\Resources\BalanceSheetResource\Pages;

use App\Filament\Owner\Resources\BalanceSheetResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\Page;

class BalanceSheets extends Page
{
    protected static string $resource = BalanceSheetResource::class;
    protected static ?string $title = 'Posisi Keuangan';
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $navigationLabel = 'Posisi Keuangan';


    protected static string $view = 'balance-sheet.balance-sheet';


    public $from;
    public $until;

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->until = now()->endOfMonth()->toDateString();
    }

    public function getBalanceSheetData()
    {
        // Group by kelompok (aset, kewajiban, ekuitas, pendapatan, beban)
        $accounts = ChartOfAccount::orderBy('kode')->get()->groupBy('kelompok');

        $data = [];

        foreach ($accounts as $kelompok => $akunList) {
            $kelompokTotal = 0;
            $rows = [];

            foreach ($akunList as $akun) {
                $details = JournalEntryDetail::where('chart_of_account_id', $akun->id)
                    ->whereHas('jurnal', function ($query) {
                        $query->whereBetween('tanggal', [$this->from, $this->until]);
                    })
                    ->get();

                $debit = $details->where('tipe', 'debit')->sum('jumlah');
                $kredit = $details->where('tipe', 'kredit')->sum('jumlah');

                // Saldo normal: debit untuk aset/beban, kredit untuk kewajiban/ekuitas/pendapatan
                if (in_array($akun->kelompok, ['aset', 'beban'])) {
                    $saldo = $debit - $kredit;
                } else {
                    $saldo = $kredit - $debit;
                }

                $kelompokTotal += $saldo;

                $rows[] = [
                    'akun' => $akun,
                    'saldo' => $saldo,
                ];
            }

            $data[] = [
                'kelompok' => $kelompok,
                'rows' => $rows,
                'total' => $kelompokTotal,
            ];
        }

        return $data;
    }
}
