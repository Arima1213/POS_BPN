<x-filament::widget>
    <x-filament::card>
        <div class="text-sm text-gray-500">Total Biaya Operasional Bulan Ini</div>
        <div class="text-2xl font-bold text-danger-600">
            Rp {{ number_format($totalBiaya, 0, ',', '.') }}
        </div>
    </x-filament::card>
</x-filament::widget>
