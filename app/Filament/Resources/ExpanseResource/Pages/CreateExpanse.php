<?php

namespace App\Filament\Resources\ExpanseResource\Pages;

use App\Filament\Resources\ExpanseResource;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateExpanse extends CreateRecord
{
    protected static string $resource = ExpanseResource::class;

    protected function afterCreate(): void
    {
        $expense = $this->record;

        DB::transaction(function () use ($expense) {
            $uniqueCode = 'BE-' . strtoupper(Str::random(8)); // Generate kode unik

            $journalEntry = JournalEntry::create([
                'tanggal' => $expense->tanggal,
                'kode' => $uniqueCode,
                'keterangan' => 'Beban: ' . $expense->deskripsi,
                'kategori' => 'beban_operasional',
            ]);

            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'chart_of_account_id' => $expense->akun_beban_id,
                'tipe' => 'debit',
                'jumlah' => $expense->jumlah,
                'deskripsi' => 'Beban: ' . $expense->deskripsi,
            ]);

            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'chart_of_account_id' => ChartOfAccount::where('kode', '1000')->first()->id, // Akun Kas
                'tipe' => 'kredit',
                'jumlah' => $expense->jumlah,
                'deskripsi' => 'Pengeluaran kas untuk beban',
            ]);
        });
    }
}
