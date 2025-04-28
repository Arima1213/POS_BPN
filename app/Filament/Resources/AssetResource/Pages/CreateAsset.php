<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;

    protected function afterCreate(): void
    {
        $asset = $this->record;

        DB::transaction(function () use ($asset) {
            $journal = JournalEntry::create([
                'tanggal' => $asset->purchase_date,
                'kode' => $asset->asset_code,
                'keterangan' => 'Pembelian Asset: ' . $asset->asset_name,
                'kategori' => 'aset',
            ]);

            $assetAccount = ChartOfAccount::where('kode', '1100')->first(); // Aset Tetap
            $cashAccount = ChartOfAccount::where('kode', '1000')->first();  // Kas Kecil

            if (!$assetAccount || !$cashAccount) {
                throw new \Exception("Akun Aset atau Kas tidak ditemukan. Mohon periksa Chart of Account.");
            }

            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $assetAccount->id,
                'tipe' => 'debit',
                'jumlah' => $asset->purchase_price,
                'deskripsi' => 'Mencatat aset baru: ' . $asset->asset_name,
            ]);

            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $cashAccount->id,
                'tipe' => 'kredit',
                'jumlah' => $asset->purchase_price,
                'deskripsi' => 'Pembayaran aset: ' . $asset->asset_name,
            ]);

            $asset->update([
                'journal_entry_id' => $journal->id,
            ]);
        });
    }
}
