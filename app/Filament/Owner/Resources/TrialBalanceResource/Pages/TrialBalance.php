<?php

namespace App\Filament\Owner\Resources\TrialBalanceResource\Pages;

use App\Filament\Owner\Resources\TrialBalanceResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\Page;

class TrialBalance extends Page
{
    protected static string $resource = TrialBalanceResource::class;
    protected static ?string $title = 'Neraca Saldo';
    protected static ?string $navigationLabel = 'Neraca Saldo';

    protected static string $view = 'trial-balance.trial-balance';

    public $from;
    public $until;

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->until = now()->endOfMonth()->toDateString();
    }

    public function getTrialBalanceData()
    {
        $details = JournalEntryDetail::with(['jurnal', 'akun'])
            ->whereHas('jurnal', function ($query) {
                $query->whereBetween('tanggal', [$this->from, $this->until]);
            })
            ->get()
            ->groupBy('chart_of_account_id');

        $result = [];

        foreach ($details as $akunId => $entries) {
            $akun = ChartOfAccount::find($akunId);
            $totalDebit = 0;
            $totalKredit = 0;

            foreach ($entries as $entry) {
                if ($entry->tipe === 'debit') {
                    $totalDebit += $entry->jumlah;
                } else {
                    $totalKredit += $entry->jumlah;
                }
            }

            $result[] = [
                'akun' => $akun,
                'debit' => $totalDebit,
                'kredit' => $totalKredit,
            ];
        }

        return $result;
    }
}
