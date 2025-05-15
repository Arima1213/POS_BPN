<?php

namespace App\Filament\Owner\Resources\DashboardPageResource\Pages;

use Filament\Resources\Pages\Page;
use Filament\Forms;
use Livewire\WithPagination;
use App\Filament\Owner\Resources\DashboardPageResource;
use App\Filament\Owner\Resources\WidgetForLifeResource\Widgets\ModalVsPrive;
use App\Filament\Owner\Resources\IncomeStatementResource\Widgets\LabaRugiTren;
use App\Filament\Owner\Resources\WidgetForLifeResource\Widgets\PendapatanBiayaPriveBulanIni;

class DashboardPage extends Page
{
    use WithPagination;

    protected static string $resource = DashboardPageResource::class;
    protected static string $view = 'filament.owner.resources.dashboard-page-resource.pages.dashboard-page';

    public ?string $from = null;
    public ?string $until = null;

    public function mount(): void
    {
        $this->from = now()->startOfMonth()->toDateString();
        $this->until = now()->endOfMonth()->toDateString();
    }

    public function getStatsData()
    {
        // Biasanya, Anda bisa trigger refresh widget atau data di sini
        // Livewire akan otomatis update data jika properti berubah
        // Jika perlu, bisa tambahkan logic lain di sini
    }

    protected function getHeaderWidgets(): array
    {
        PendapatanBiayaPriveBulanIni::setFilters($this->from, $this->until);
        ModalVsPrive::setFilters($this->from, $this->until);
        LabaRugiTren::setFilters($this->from, $this->until);

        return [
            PendapatanBiayaPriveBulanIni::make(),
            ModalVsPrive::make(),
            LabaRugiTren::make(),
        ];
    }
}
