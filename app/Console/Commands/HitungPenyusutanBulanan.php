<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Asset;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Illuminate\Console\Command;
use App\Models\JournalEntryDetail;
use Illuminate\Support\Facades\DB;

class HitungPenyusutanBulanan extends Command
{
    protected $signature = 'aset:penyusutan';
    protected $description = 'Hitung penyusutan bulanan untuk aset tetap aktif';

    public function handle()
    {
        $aset = Asset::where('status', 'active')->get();
        $tanggal = Carbon::now()->startOfMonth();

        $akunBeban = ChartOfAccount::where('kode', '5020')->first(); // Beban Penyusutan
        $akunAkumulasi = ChartOfAccount::where('kode', '1990')->first(); // Akumulasi Penyusutan

        foreach ($aset as $a) {
            $umurBulan = $a->useful_life_years * 12;
            if ($umurBulan <= 0) continue;

            $penyusutanPerBulan = ($a->purchase_price - $a->residual_value) / $umurBulan;

            if ($a->accumulated_depreciation + $penyusutanPerBulan > $a->purchase_price) {
                continue; // Tidak melebihi nilai buku
            }

            DB::transaction(function () use ($a, $penyusutanPerBulan, $akunBeban, $akunAkumulasi, $tanggal) {
                $journal = JournalEntry::create([
                    'tanggal' => $tanggal,
                    'kode' => 'DEP-' . $a->asset_code,
                    'keterangan' => 'Penyusutan bulan ' . $tanggal->format('F Y') . ' untuk ' . $a->asset_name,
                    'kategori' => 'penyusutan',
                ]);

                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => $akunBeban->id,
                    'tipe' => 'debit',
                    'jumlah' => $penyusutanPerBulan,
                    'deskripsi' => 'Beban penyusutan ' . $a->asset_name,
                ]);

                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => $akunAkumulasi->id,
                    'tipe' => 'kredit',
                    'jumlah' => $penyusutanPerBulan,
                    'deskripsi' => 'Akumulasi penyusutan ' . $a->asset_name,
                ]);

                $a->update([
                    'accumulated_depreciation' => $a->accumulated_depreciation + $penyusutanPerBulan,
                ]);
            });
        }

        $this->info('Penyusutan bulanan berhasil diproses.');
    }
}
