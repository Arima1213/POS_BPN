<x-filament-panels::page>
 <form wire:submit.prevent="getStatsData" class="space-y-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::input
                type="date"
                wire:model.defer="from"
                label="Tanggal Awal"
            />
            <x-filament::input
                type="date"
                wire:model.defer="until"
                label="Tanggal Akhir"
            />
            <div class="flex items-end gap-3">
                <x-filament::button type="submit" class="w-full md:w-auto">
                    Tampilkan
                </x-filament::button>
            </div>
        </div>
    </form>
</x-filament-panels::page>
