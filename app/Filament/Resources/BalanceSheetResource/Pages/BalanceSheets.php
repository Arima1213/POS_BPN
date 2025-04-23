<?php

namespace App\Filament\Resources\BalanceSheetResource\Pages;

use App\Filament\Resources\BalanceSheetResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\Page;

class BalanceSheets extends Page
{
    protected static string $resource = BalanceSheetResource::class;

    protected static string $view = 'balance-sheets.balance-sheets';

    public $tanggal;

    public function mount(): void
    {
        $this->tanggal = now()->toDateString();
    }

    public function getBalanceSheetData()
    {
        $accounts = ChartOfAccount::all()->groupBy('kategori');

        $data = [];

        foreach ($accounts as $kategori => $akunList) {
            $kategoriTotal = 0;
            $rows = [];

            foreach ($akunList as $akun) {
                $details = JournalEntryDetail::where('chart_of_account_id', $akun->id)
                    ->whereHas('jurnal', function ($query) {
                        $query->whereDate('tanggal', '<=', $this->tanggal);
                    })
                    ->get();

                $debit = $details->where('tipe', 'debit')->sum('jumlah');
                $kredit = $details->where('tipe', 'kredit')->sum('jumlah');

                $saldo = $akun->jenis_saldo === 'debit' ? $debit - $kredit : $kredit - $debit;

                $kategoriTotal += $saldo;

                $rows[] = [
                    'akun' => $akun,
                    'saldo' => $saldo,
                ];
            }

            $data[] = [
                'kategori' => $kategori,
                'rows' => $rows,
                'total' => $kategoriTotal,
            ];
        }

        return $data;
    }
}
