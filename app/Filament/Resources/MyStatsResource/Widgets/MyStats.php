<?php

namespace App\Filament\Resources\MyStatsResource\Widgets;

use App\Models\JournalEntryDetail;
use App\Models\Transactions;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class MyStats extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();

        // 1. Jumlah pendapatan hari ini (akun kelompok = pendapatan)
        $pendapatanHariIni = JournalEntryDetail::whereHas(
            'akun',
            fn($q) =>
            $q->where('kelompok', 'pendapatan')
        )
            ->whereHas(
                'jurnal',
                fn($q) =>
                $q->whereDate('tanggal', $today)
            )
            ->get()
            ->sum(fn($d) => $d->tipe === 'kredit' ? $d->jumlah : -$d->jumlah);

        // 2. Jumlah pengeluaran hari ini (akun beban + pengadaan jika dicatat dalam jurnal)
        $pengeluaranHariIni = JournalEntryDetail::whereHas(
            'akun',
            fn($q) =>
            $q->whereIn('kelompok', ['beban']) // pengeluaran biasanya beban atau pembelian barang (aset)
        )
            ->whereHas(
                'jurnal',
                fn($q) =>
                $q->whereDate('tanggal', $today)
            )
            ->get()
            ->sum(fn($d) => $d->tipe === 'debit' ? $d->jumlah : -$d->jumlah);

        // 3. Jumlah transaksi hari ini
        $jumlahTransaksiHariIni = Transactions::whereDate('created_at', $today)->count();

        return [
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($pendapatanHariIni, 0, ',', '.'))
                ->description('Dari transaksi penjualan dan lainnya')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('success'),

            Stat::make('Pengeluaran Hari Ini', 'Rp ' . number_format($pengeluaranHariIni, 0, ',', '.'))
                ->description('Dari Operasional')
                ->icon('heroicon-o-arrow-down-circle')
                ->color('danger'),

            Stat::make('Transaksi Hari Ini', number_format($jumlahTransaksiHariIni))
                ->description('Total transaksi tercatat hari ini')
                ->icon('heroicon-o-receipt-refund')
                ->color('primary'),
        ];
    }
}
