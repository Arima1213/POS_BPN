<x-filament::widget>
    <x-filament::card class="relative overflow-hidden bg-gradient-to-r from-green-100 to-green-200 shadow-lg">

        <div class="text-lg font-semibold text-gray-700 dark:text-gray-200">Total Pendapatan Bulan Ini</div>
        <div class="text-2xl font-bold text-green-700 mt-2">
            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </div>
        <div class="mt-4 text-sm text-gray-500">*Data diperbarui secara berkala</div>
    </x-filament::card>
</x-filament::widget>
