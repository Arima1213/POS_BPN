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
                        $sudahList = \App\Models\Asset::where('status', 'active')
                            ->where('is_fully_depreciated', true)
                            ->pluck('asset_name')
                            ->toArray();

                        \Filament\Notifications\Notification::make()
                            ->title('Penyusutan bulan ini sudah dilakukan.')
                            ->body('Aset yang sudah terupdate: ' . (count($sudahList) ? implode(', ', $sudahList) : '-'))
                            ->danger()
                            ->send();
                        return;
                    }

                    $aset = \App\Models\Asset::where('status', 'active')->get();
                    $akunBeban = \App\Models\ChartOfAccount::where('kode', '5020')->first();
                    $akunAkumulasi = \App\Models\ChartOfAccount::where('kode', '1990')->first();

                    $updatedAssets = [];
                    $skippedAssets = [];

                    foreach ($aset as $a) {
                        $umurBulan = $a->useful_life_years * 12;
                        if ($umurBulan <= 0) {
                            $skippedAssets[] = $a->asset_name;
                            continue;
                        }

                        $penyusutanPerBulan = ($a->purchase_price - $a->residual_value) / $umurBulan;

                        if ($a->accumulated_depreciation + $penyusutanPerBulan > $a->purchase_price) {
                            $skippedAssets[] = $a->asset_name;
                            continue;
                        }

                        DB::transaction(function () use ($a, $penyusutanPerBulan, $akunBeban, $akunAkumulasi, $tanggal) {
                            $journal = \App\Models\JournalEntry::create([
                                'tanggal' => $tanggal,
                                'kode' => 'DEP-' . $a->asset_code,
                                'keterangan' => 'Penyusutan bulan ' . $tanggal->format('F Y') . ' untuk ' . $a->asset_name,
                                'kategori' => 'penyesuaian',
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

                            // Hitung nilai sisa (book value)
                            $newAccumulated = $a->accumulated_depreciation + $penyusutanPerBulan;
                            $bookValue = $a->purchase_price - $newAccumulated;

                            // Cek apakah sudah fully depreciated
                            $isFullyDepreciated = false;
                            if ($newAccumulated >= ($a->purchase_price - $a->residual_value)) {
                                $isFullyDepreciated = true;
                                $bookValue = $a->residual_value;
                            }

                            $a->update([
                                'accumulated_depreciation' => $newAccumulated,
                                'is_fully_depreciated' => $isFullyDepreciated ? 1 : 0,
                                'journal_entry_id' => $journal->id,
                                'depreciation_start_date' => $tanggal,
                                'depreciation_method' => 'straight_line',
                            ]);
                        });

                        $updatedAssets[] = $a->asset_name;
                    }

                    // Tambahkan aset yang sudah fully depreciated ke skippedAssets
                    $fullyDepreciated = $aset->where('is_fully_depreciated', true)->pluck('asset_name')->toArray();
                    foreach ($fullyDepreciated as $fd) {
                        if (!in_array($fd, $skippedAssets) && !in_array($fd, $updatedAssets)) {
                            $skippedAssets[] = $fd;
                        }
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Penyusutan bulanan diproses.')
                        ->body(
                            'Aset yang berhasil diupdate: ' . (count($updatedAssets) ? implode(', ', $updatedAssets) : '-') .
                                "\nAset yang sudah terupdate/skip: " . (count($skippedAssets) ? implode(', ', $skippedAssets) : '-')
                        )
                        ->success()
                        ->send();
                }),
        ];
    }
}