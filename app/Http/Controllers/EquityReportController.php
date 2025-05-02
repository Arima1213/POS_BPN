<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\JournalEntryDetail;
use App\Http\Controllers\Controller;

class EquityReportController extends Controller
{
    public function download(Request $request)
    {
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $until = $request->query('until', now()->endOfMonth()->toDateString());

        $akunModal = ChartOfAccount::where('kode', '3000')->first();
        $akunPrive = ChartOfAccount::where('kode', '3010')->first();

        $modalAwal = $this->getSaldoAkun($akunModal?->id, '<', $from);
        $penambahanModal = $this->getMutasiModal($akunModal?->id, $from, $until);
        $totalPrive = $this->getTotalPrive($akunPrive?->id, $from, $until);
        $labaBersih = $this->getLabaBersih($from, $until);

        $modalAkhir = $modalAwal + $penambahanModal + $labaBersih - $totalPrive;

        $reportData = compact('modalAwal', 'penambahanModal', 'labaBersih', 'totalPrive', 'modalAkhir', 'from', 'until');

        $pdf = Pdf::loadView('pdf.changes-in-equity', $reportData);

        return $pdf->download('Perubahan_Ekuitas_' . now()->format('Ymd') . '.pdf');
    }

    private function getSaldoAkun($akunId, $operator, $date)
    {
        if (!$akunId) return 0;

        return JournalEntryDetail::where('chart_of_account_id', $akunId)
            ->whereHas('jurnal', fn($q) => $q->where('tanggal', $operator, $date))
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? -$d->jumlah : $d->jumlah);
    }

    private function getMutasiModal($akunId, $from, $until)
    {
        if (!$akunId) return 0;

        return JournalEntryDetail::where('chart_of_account_id', $akunId)
            ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$from, $until]))
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? -$d->jumlah : $d->jumlah);
    }

    private function getTotalPrive($akunId, $from, $until)
    {
        if (!$akunId) return 0;

        return JournalEntryDetail::where('chart_of_account_id', $akunId)
            ->where('tipe', 'debit')
            ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$from, $until]))
            ->sum('jumlah');
    }

    private function getLabaBersih($from, $until): float
    {
        $pendapatan = JournalEntryDetail::whereHas('akun', fn($q) => $q->where('kelompok', 'pendapatan'))
            ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$from, $until]))
            ->get()
            ->sum(fn($d) => $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah);

        $beban = JournalEntryDetail::whereHas('akun', fn($q) => $q->where('kelompok', 'beban'))
            ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$from, $until]))
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? $d->jumlah : -$d->jumlah);

        return $pendapatan - $beban;
    }
}
