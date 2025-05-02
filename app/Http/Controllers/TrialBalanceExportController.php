<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TrialBalanceExportController extends Controller
{
    public function download(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $until = $request->input('until', now()->endOfMonth()->toDateString());

        $details = JournalEntryDetail::with(['jurnal', 'akun'])
            ->whereHas('jurnal', function ($query) use ($from, $until) {
                $query->whereBetween('tanggal', [$from, $until]);
            })
            ->get()
            ->groupBy('chart_of_account_id');

        $data = [];

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

            $data[] = [
                'akun' => $akun,
                'debit' => $totalDebit,
                'kredit' => $totalKredit,
            ];
        }

        $pdf = Pdf::loadView('pdf.trial-balance', [
            'from' => $from,
            'until' => $until,
            'rows' => $data,
        ]);

        return $pdf->download("Neraca_Saldo_{$from}_sampai_{$until}.pdf");
    }
}
