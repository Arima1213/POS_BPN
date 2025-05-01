<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class GeneralLedgerPrintController extends Controller
{
    public function download(Request $request)
    {
        $from = $request->query('from');
        $until = $request->query('until');

        $details = JournalEntryDetail::with(['jurnal', 'akun'])
            ->whereHas('jurnal', function ($query) use ($from, $until) {
                $query->whereBetween('tanggal', [$from, $until]);
            })
            ->get()
            ->sortBy(fn($d) => $d->jurnal->tanggal)
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

                $runningSaldo += $debit - $kredit;

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

        $pdf = Pdf::loadView('pdf.general-ledger', [
            'ledger' => $ledger,
            'from' => $from,
            'until' => $until,
        ]);

        return $pdf->download("Buku_Besar_{$from}_sd_{$until}.pdf");
    }
}
