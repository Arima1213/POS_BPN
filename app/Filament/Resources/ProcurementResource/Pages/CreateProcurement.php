<?php

namespace App\Filament\Resources\ProcurementResource\Pages;

use App\Filament\Resources\ProcurementResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\ProductStock;
use App\Models\Stock;
use App\Models\StockHistory;
use Filament\Resources\Pages\CreateRecord;

class CreateProcurement extends CreateRecord
{
    protected static string $resource = ProcurementResource::class;

    protected function afterCreate(): void
    {
        $procurement = $this->record;

        // --- Update stock
        $stock = ProductStock::firstOrCreate(
            ['product_id' => $procurement->product_id],
            ['current_stock' => 0, 'minimum_stock' => 0]
        );

        $stock->current_stock += $procurement->quantity;
        $stock->save();

        // --- Simpan ke stock history
        StockHistory::create([
            'product_id' => $procurement->product_id,
            'type' => 'in', // masuk
            'quantity' => $procurement->quantity,
            'note' => 'Pengadaan Barang #' . $procurement->id,
        ]);

        // --- Catat ke jurnal akuntansi
        $totalAmount = $procurement->price * $procurement->quantity;

        // Cari akun persediaan
        $inventoryAccount = ChartOfAccount::where('nama', 'Persediaan Pupuk dan Benih')->first();
        if (!$inventoryAccount) {
            throw new \Exception('Akun Persediaan belum ada, silakan tambah ke Chart of Accounts.');
        }

        // Cari akun kas kecil
        $cashAccount = ChartOfAccount::where('nama', 'Kas Kecil')->first();
        if (!$cashAccount) {
            throw new \Exception('Akun Kas Kecil belum ada, silakan tambah ke Chart of Accounts.');
        }

        // Buat entri jurnal
        $journal = JournalEntry::create([
            'tanggal' => now(),
            'kode' => 'PRC-' . now()->format('Ymd') . '-' . $procurement->id,
            'keterangan' => 'Pengadaan Barang #' . $procurement->id,
            'kategori' => 'aset',
        ]);

        // Buat detail debit (nambah aset persediaan)
        JournalEntryDetail::create([
            'journal_entry_id' => $journal->id,
            'chart_of_account_id' => $inventoryAccount->id,
            'tipe' => 'debit',
            'jumlah' => $totalAmount,
            'deskripsi' => 'Penambahan stok produk ID #' . $procurement->product_id,
        ]);

        // Buat detail kredit (mengurangi kas kecil)
        \App\Models\JournalEntryDetail::create([
            'journal_entry_id' => $journal->id,
            'chart_of_account_id' => $cashAccount->id,
            'tipe' => 'kredit',
            'jumlah' => $totalAmount,
            'deskripsi' => 'Pembayaran pengadaan produk ID #' . $procurement->product_id,
        ]);
    }
}