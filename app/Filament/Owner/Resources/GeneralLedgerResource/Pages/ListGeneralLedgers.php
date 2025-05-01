<?php

namespace App\Filament\Owner\Resources\GeneralLedgerResource\Pages;

use App\Filament\Owner\Resources\GeneralLedgerResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListGeneralLedgers extends ListRecords
{
    protected static string $resource = GeneralLedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->url(fn() => route('general-ledger.download.pdf', [
                    'from' => $this->from,
                    'until' => $this->until,
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
