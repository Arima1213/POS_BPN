<?php

namespace App\Filament\Owner\Resources\TransactionResource\Pages;

use App\Filament\Owner\Resources\TransactionResource;
use Filament\Actions;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{

    public function getTitle(): string
    {
        return 'Tambah Transaksi';
    }

    protected static string $resource = TransactionResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Buat Journal Entry
        $jurnal = JournalEntry::create([
            'tanggal' => $record->tanggal,
            'kode' => 'JE-' . str_pad(JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT),
            'keterangan' => $record->keterangan ?? ($record->tipe === 'setor_modal' ? 'Setor Modal' : 'Penarikan Prive'),
            'kategori' => 'lainnya',
        ]);

        if ($record->tipe === 'setor_modal') {
            $akunKas = ChartOfAccount::where('kode', '1000')->first();
            $akunModal = ChartOfAccount::where('kode', '3000')->first();

            // Kas bertambah
            JournalEntryDetail::create([
                'journal_entry_id' => $jurnal->id,
                'chart_of_account_id' => $akunKas->id,
                'tipe' => 'debit',
                'jumlah' => $record->jumlah,
                'deskripsi' => 'Setor Modal',
            ]);

            // Modal bertambah
            JournalEntryDetail::create([
                'journal_entry_id' => $jurnal->id,
                'chart_of_account_id' => $akunModal->id,
                'tipe' => 'kredit',
                'jumlah' => $record->jumlah,
                'deskripsi' => 'Setor Modal',
            ]);
        } elseif ($record->tipe === 'prive') {
            $akunKas = ChartOfAccount::where('kode', '1000')->first();
            $akunPrive = ChartOfAccount::where('kode', '3010')->first();

            // Prive bertambah
            JournalEntryDetail::create([
                'journal_entry_id' => $jurnal->id,
                'chart_of_account_id' => $akunPrive->id,
                'tipe' => 'debit',
                'jumlah' => $record->jumlah,
                'deskripsi' => 'Penarikan Prive',
            ]);

            // Kas berkurang
            JournalEntryDetail::create([
                'journal_entry_id' => $jurnal->id,
                'chart_of_account_id' => $akunKas->id,
                'tipe' => 'kredit',
                'jumlah' => $record->jumlah,
                'deskripsi' => 'Penarikan Prive',
            ]);
        }
    }
}
