<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Models\JournalEntry;
use App\Models\ProductStock;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\ProductResource;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function afterCreate(): void
    {
        $procurement = $this->record;
        $jumlah = $procurement->quantity * $procurement->price;

        // Ambil akun untuk Persediaan dan Kas Kecil
        $akunPersediaan = ChartOfAccount::where('kode', '1031')->first();
        $akunKas = ChartOfAccount::where('kode', '1000')->first(); // Kas Kecil

        if (!$akunPersediaan || !$akunKas) {
            return; // Tidak buat jurnal kalau akun tidak ditemukan
        }

        // Buat entri jurnal
        $jurnal = JournalEntry::create([
            'tanggal' => $procurement->procurement_date,
            'kode' => 'JE-' . strtoupper(Str::random(6)),
            'kategori' => 'pengadaan',
            'deskripsi' => 'Pengadaan barang: ' . $procurement->product->name,
        ]);

        JournalEntryDetail::create([
            'journal_entry_id' => $jurnal->id,
            'chart_of_account_id' => $akunPersediaan->id,
            'tipe' => 'debit',
            'jumlah' => $jumlah,
            'deskripsi' => 'Pengadaan ' . $procurement->product->name,
        ]);

        JournalEntryDetail::create([
            'journal_entry_id' => $jurnal->id,
            'chart_of_account_id' => $akunKas->id,
            'tipe' => 'kredit',
            'jumlah' => $jumlah,
            'deskripsi' => 'Pembayaran Pengadaan ' . $procurement->product->name,
        ]);
    }
}