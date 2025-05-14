<?php

namespace App\Filament\Resources\AssetResource\Pages;

use App\Filament\Resources\AssetResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('hitung_penyusutan')
                ->label('Hitung Penyusutan Bulanan')
                ->color('warning')
                ->icon('heroicon-o-calculator')
                ->requiresConfirmation()
                ->action(function () {
                    $tanggal = \Carbon\Carbon::now()->startOfMonth();

                    // Cek apakah sudah ada jurnal penyusutan bulan ini
                    $sudahAda = \App\Models\JournalEntry::where('kategori', 'penyusutan')
                        ->whereMonth('tanggal', $tanggal->month)
                        ->whereYear('tanggal', $tanggal->year)
                        ->exists();

                    if ($sudahAda) {
                        \Filament\Notifications\Notification::make()
                            ->title('Penyusutan bulan ini sudah dilakukan.')
                            ->danger()
                            ->send();
                        return;
                    }

                    $aset = \App\Models\Asset::where('status', 'active')->get();
                    $akunBeban = \App\Models\ChartOfAccount::where('kode', '5020')->first();
                    $akunAkumulasi = \App\Models\ChartOfAccount::where('kode', '1990')->first();

                    foreach ($aset as $a) {
                        $umurBulan = $a->useful_life_years * 12;
                        if ($umurBulan <= 0) continue;

                        $penyusutanPerBulan = ($a->purchase_price - $a->residual_value) / $umurBulan;

                        if ($a->accumulated_depreciation + $penyusutanPerBulan > $a->purchase_price) {
                            continue;
                        }

                        DB::transaction(function () use ($a, $penyusutanPerBulan, $akunBeban, $akunAkumulasi, $tanggal) {
                            $journal = \App\Models\JournalEntry::create([
                                'tanggal' => $tanggal,
                                'kode' => 'DEP-' . $a->asset_code,
                                'keterangan' => 'Penyusutan bulan ' . $tanggal->format('F Y') . ' untuk ' . $a->asset_name,
                                'kategori' => 'penyusutan',
                            ]);

                            \App\Models\JournalEntryDetail::create([
                                'journal_entry_id' => $journal->id,
                                'chart_of_account_id' => $akunBeban->id,
                                'tipe' => 'debit',
                                'jumlah' => $penyusutanPerBulan,
                                'deskripsi' => 'Beban penyusutan ' . $a->asset_name,
                            ]);

                            \App\Models\JournalEntryDetail::create([
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

                    \Filament\Notifications\Notification::make()
                        ->title('Penyusutan bulanan berhasil diproses.')
                        ->success()
                        ->send();
                }),
        ];
    }
}