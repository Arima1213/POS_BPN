<?php

namespace App\Filament\Cashier\Resources\TransactionsResource\Pages;

use App\Models\Debt;
use App\Models\Customer;
use Illuminate\Support\Str;
use App\Models\JournalEntry;
use App\Models\Transactions;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Cashier\Resources\TransactionsResource;

class CreateTransactions extends CreateRecord
{
    protected static string $resource = TransactionsResource::class;

    public function getTitle(): string
    {
        return 'Tambah Transaksi';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Cek apakah user tambah customer baru
        if ($data['add_new_customer'] ?? false) {
            $customer = Customer::create([
                'name'  => $data['new_customer_name'],
                'phone' => $data['new_customer_phone'],
            ]);
            $data['customer_id'] = $customer->id;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Transactions $record */
        $record = $this->record;

        // Kurangi stok produk terkait
        foreach ($record->details as $detail) {
            if ($detail->item_type === 'product') {
                $product = \App\Models\Product::find($detail->item_id);
                if ($product && $product->productStock) {
                    $product->productStock->decrement('current_stock', $detail->quantity);
                }
            }
        }

        // Jika uang pembeli kurang dari total, maka buatkan hutang
        if ($record->paid_amount < $record->total) {
            Debt::create([
                'customer_id'    => $record->customer_id,
                'transaction_id' => $record->id,
                'amount'         => $record->total,
                'paid'           => $record->paid_amount,
                'due_date'       => now()->addDays(30),
            ]);
        }

        $transaction = $record;

        // Buat entri jurnal utama
        $journal = JournalEntry::create([
            'tanggal' => now(),
            'kode' => 'JE-' . strtoupper(Str::random(6)),
            'keterangan' => 'Transaksi Penjualan: ' . $transaction->code,
            'kategori' => 'aset',
        ]);

        // Hitung total produk dan jasa
        $totalProduk = 0;
        $totalJasa = 0;

        foreach ($transaction->details as $detail) {
            if ($detail->item_type === 'product') {
                $totalProduk += $detail->subtotal;
            } elseif ($detail->item_type === 'service') {
                $totalJasa += $detail->subtotal;
            }
        }

        $total = $transaction->total;
        $paid = $transaction->paid_amount;
        $change = $transaction->change_amount;

        // === JURNAL ENTRIES ===

        // 1. Debit: Kas Kecil untuk jumlah yang dibayar tunai
        if ($paid > 0) {
            if ($paid > $total) {
                // Debit: Kas Kecil untuk jumlah yang dibayar tunai
                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => ChartOfAccount::where('kode', '1000')->value('id'),
                    'tipe' => 'debit',
                    'jumlah' => $paid,
                    'deskripsi' => 'Penerimaan kas dari transaksi ' . $transaction->code,
                ]);

                // Kredit: Kembalian
                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => ChartOfAccount::where('kode', '1040')->value('id'),
                    'tipe' => 'kredit',
                    'jumlah' => $change,
                    'deskripsi' => 'Kembalian untuk transaksi ' . $transaction->code,
                ]);
            } else {
                // Debit: Kas Kecil untuk jumlah yang dibayar tunai
                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => ChartOfAccount::where('kode', '1000')->value('id'),
                    'tipe' => 'debit',
                    'jumlah' => $paid,
                    'deskripsi' => 'Penerimaan kas dari transaksi ' . $transaction->code,
                ]);
            }
        }

        // 2. Jika ada kekurangan bayar, Debit ke Piutang Produk dan/atau Jasa
        if ($change < 0) {
            $hutangProduk = ($totalProduk / $total) * abs($change);
            $hutangJasa = ($totalJasa / $total) * abs($change);

            if ($hutangProduk > 0) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => ChartOfAccount::where('kode', '1020')->value('id'),
                    'tipe' => 'debit',
                    'jumlah' => round($hutangProduk, 2),
                    'deskripsi' => 'Piutang Produk - Transaksi ' . $transaction->code,
                ]);
            }

            if ($hutangJasa > 0) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => ChartOfAccount::where('kode', '1021')->value('id'),
                    'tipe' => 'debit',
                    'jumlah' => round($hutangJasa, 2),
                    'deskripsi' => 'Piutang Jasa - Transaksi ' . $transaction->code,
                ]);
            }
        }

        // 3. Kredit: Pendapatan Produk
        if ($totalProduk > 0) {
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => ChartOfAccount::where('kode', '4000')->value('id'),
                'tipe' => 'kredit',
                'jumlah' => round($totalProduk, 2),
                'deskripsi' => 'Pendapatan Produk - Transaksi ' . $transaction->code,
            ]);
        }

        // 4. Kredit: Pendapatan Jasa
        if ($totalJasa > 0) {
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => ChartOfAccount::where('kode', '4010')->value('id'),
                'tipe' => 'kredit',
                'jumlah' => round($totalJasa, 2),
                'deskripsi' => 'Pendapatan Jasa - Transaksi ' . $transaction->code,
            ]);
        }

        $this->redirectRoute('transactions.print.receipt', ['transaction' => $record]);
    }
}