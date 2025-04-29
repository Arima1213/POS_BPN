<x-filament::widget>
    <x-filament::card class="relative overflow-hidden bg-gradient-to-r from-blue-100 to-blue-200 shadow-lg">

        <div class="text-lg font-semibold text-gray-700 dark:text-gray-200">Total Biaya Operasional Bulan Ini</div>
        <div class="text-2xl font-bold text-blue-700 mt-2">
            Rp {{ number_format($totalBiaya, 0, ',', '.') }}
        </div>
        <div class="mt-4 text-sm text-gray-500">*Data diperbarui secara berkala</div>
    </x-filament::card>
</x-filament::widget>
