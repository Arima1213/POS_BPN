<x-filament::widget>
    <x-filament::card>
        <div class="text-lg font-semibold text-gray-700">Total Pendapatan Bulan Ini</div>
        <div class="text-2xl font-bold text-green-600 mt-2">
            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </div>
    </x-filament::card>
</x-filament::widget>
