<?php

namespace App\Http\Controllers\Owner;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\JournalEntryDetail;
use App\Http\Controllers\Controller;

class IncomeStatementExportController extends Controller
{
    public function download(Request $request)
    {
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $until = $request->query('until', now()->endOfMonth()->toDateString());

        $details = JournalEntryDetail::with(['akun', 'jurnal'])
            ->whereHas('jurnal', function ($q) use ($from, $until) {
                $q->whereBetween('tanggal', [$from, $until]);
            })
            ->get();

        $pendapatan = $details->filter(fn($item) => str($item->akun->kode)->startsWith('4'))
            ->groupBy('akun.id')
            ->map(function ($items) {
                $akun = $items->first()->akun;
                $total = $items->sum(fn($i) => $i->tipe === 'kredit' ? $i->jumlah : -$i->jumlah);
                return compact('akun', 'total');
            });

        $biaya = $details->filter(fn($item) => str($item->akun->kode)->startsWith('5'))
            ->groupBy('akun.id')
            ->map(function ($items) {
                $akun = $items->first()->akun;
                $total = $items->sum(fn($i) => $i->tipe === 'debit' ? $i->jumlah : -$i->jumlah);
                return compact('akun', 'total');
            });

        $totalPendapatan = $pendapatan->sum('total');
        $totalBiaya = $biaya->sum('total');
        $labaBersih = $totalPendapatan - $totalBiaya;

        $pdf = Pdf::loadView('pdf.income-statement', compact(
            'from',
            'until',
            'pendapatan',
            'biaya',
            'totalPendapatan',
            'totalBiaya',
            'labaBersih'
        ));

        return $pdf->download("Income_Statement_{$from}_to_{$until}.pdf");
    }
}
