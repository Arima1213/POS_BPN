<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use App\Models\JournalEntry;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditAsset extends EditRecord
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterRecordUpdated(): void
    {
        $asset = $this->record;

        if (!$asset->journal_entry_id) {
            return; // Kalau tidak ada jurnal, skip
        }

        DB::transaction(function () use ($asset) {
            $journal = JournalEntry::find($asset->journal_entry_id);

            if ($journal) {
                $journal->update([
                    'tanggal' => $asset->purchase_date,
                    'kode' => $asset->asset_code,
                    'keterangan' => 'Update Pembelian Asset: ' . $asset->asset_name,
                    'kategori' => 'aset',
                ]);

                // Update detail jurnal
                $details = $journal->journalEntryDetails;

                foreach ($details as $detail) {
                    if ($detail->tipe === 'debit') {
                        $detail->update([
                            'jumlah' => $asset->purchase_price,
                            'deskripsi' => 'Update aset baru: ' . $asset->asset_name,
                        ]);
                    } elseif ($detail->tipe === 'kredit') {
                        $detail->update([
                            'jumlah' => $asset->purchase_price,
                            'deskripsi' => 'Update pembayaran aset: ' . $asset->asset_name,
                        ]);
                    }
                }
            }
        });
    }
}