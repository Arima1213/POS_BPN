<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\JournalEntryDetail;

class BalanceSheetExportController extends Controller
{
    public function download(Request $request)
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $until = $request->input('until', now()->toDateString());

        $accounts = ChartOfAccount::orderBy('kode')->get()->groupBy('kelompok');
        $data = [];

        foreach ($accounts as $kelompok => $akunList) {
            $kelompokTotal = 0;
            $rows = [];

            foreach ($akunList as $akun) {
                $details = JournalEntryDetail::where('chart_of_account_id', $akun->id)
                    ->whereHas('jurnal', function ($query) use ($from, $until) {
                        $query->whereBetween('tanggal', [$from, $until]);
                    })
                    ->get();

                $debit = $details->where('tipe', 'debit')->sum('jumlah');
                $kredit = $details->where('tipe', 'kredit')->sum('jumlah');

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

        $pdf = Pdf::loadView('pdf.balance-sheet', [
            'from' => $from,
            'until' => $until,
            'data' => $data,
        ]);

        return $pdf->download("Neraca_{$from}_sampai_{$until}.pdf");
    }
}
