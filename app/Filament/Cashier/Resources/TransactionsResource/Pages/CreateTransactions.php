<?php

namespace App\Filament\Cashier\Resources\TransactionsResource\Pages;

use Carbon\Carbon;
use App\Models\Debt;
use App\Models\Customer;
use Illuminate\Support\Str;
use App\Models\JournalEntry;
use App\Models\Transactions;
use App\Models\ChartOfAccount;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Cashier\Resources\TransactionsResource;
use App\Models\Transactions_Details;
use Filament\Forms\Form;

class CreateTransactions extends CreateRecord
{
    protected static string $resource = TransactionsResource::class;

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
        $details = $this->form->getState()['details'] ?? [];

        foreach ($details as $detail) {
            Transactions_Details::create([
                'transaction_id' => $record->id,
                'item_type' => $detail['item_type'],
                'item_id' => $detail['item_id'],
                'price' => $detail['price'],
                'quantity' => $detail['quantity'],
                'subtotal' => $detail['subtotal'],
            ]);
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

        $transaction = $this->record;

        // Buat entri jurnal baru
        $journal = JournalEntry::create([
            'tanggal' => Carbon::now(),
            'kode' => 'JE-' . strtoupper(Str::random(6)),
            'keterangan' => 'Transaksi Penjualan: ' . $transaction->code,
            'kategori' => 'aset',
        ]);

        // Total pendapatan produk & jasa
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

        // 1. Debit Kas Kecil (uang yang diterima)
        if ($paid > 0) {
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => ChartOfAccount::where('kode', '1000')->value('id'),
                'tipe' => 'debit',
                'jumlah' => $paid - max(0, -$change), // Hanya yang dibayar tunai
                'deskripsi' => 'Pembayaran tunai transaksi ' . $transaction->code,
            ]);
        }

        // 2. Jika terdapat kekurangan bayar, masukkan ke Piutang (Produk atau Jasa)
        if ($change < 0) {
            $hutangProduk = $totalProduk / $total * abs($change);
            $hutangJasa = $totalJasa / $total * abs($change);

            if ($hutangProduk > 0) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => ChartOfAccount::where('kode', '1020')->value('id'),
                    'tipe' => 'debit',
                    'jumlah' => round($hutangProduk, 2),
                    'deskripsi' => 'Piutang Produk transaksi ' . $transaction->code,
                ]);
            }

            if ($hutangJasa > 0) {
                JournalEntryDetail::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => ChartOfAccount::where('kode', '1021')->value('id'),
                    'tipe' => 'debit',
                    'jumlah' => round($hutangJasa, 2),
                    'deskripsi' => 'Piutang Jasa transaksi ' . $transaction->code,
                ]);
            }
        }

        // 3. Kredit Pendapatan Penjualan Produk
        if ($totalProduk > 0) {
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => ChartOfAccount::where('kode', '4000')->value('id'),
                'tipe' => 'kredit',
                'jumlah' => round($totalProduk, 2),
                'deskripsi' => 'Pendapatan Produk dari transaksi ' . $transaction->code,
            ]);
        }

        // 4. Kredit Pendapatan Jasa Giling
        if ($totalJasa > 0) {
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => ChartOfAccount::where('kode', '4010')->value('id'),
                'tipe' => 'kredit',
                'jumlah' => round($totalJasa, 2),
                'deskripsi' => 'Pendapatan Jasa dari transaksi ' . $transaction->code,
            ]);
        }

        $this->redirectRoute('transactions.print.receipt', ['transaction' => $record]);
    }
}
