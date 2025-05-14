<x-filament::page>
    <form wire:submit.prevent="loadReport" class="space-y-4 mb-6">
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
                <x-filament::button
                    tag="a"
                    href="{{ route('owner.equity.download.pdf', ['from' => $from, 'until' => $until]) }}"
                    target="_blank"
                    color="gray"
                    class="w-full md:w-auto"
                >
                    PDF
                </x-filament::button>
            </div>
        </div>
    </form>

    <div class="rounded-xl shadow ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900 p-4">
        <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-gray-100">
            Laporan Perubahan Ekuitas
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-gray-800 dark:text-gray-100 table-auto">
                <tbody>
                    <tr>
                        <td class="border px-4 py-2">Modal Awal</td>
                        <td class="border px-4 py-2 text-right">{{ number_format($reportData['modal_awal'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">Penambahan Modal</td>
                        <td class="border px-4 py-2 text-right">{{ number_format($reportData['penambahan_modal'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">Laba Bersih</td>
                        <td class="border px-4 py-2 text-right">{{ number_format($reportData['laba_bersih'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="border px-4 py-2">Prive</td>
                        <td class="border px-4 py-2 text-right">{{ number_format($reportData['prive'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="font-semibold bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-600">
                        <td class="border px-4 py-3">Modal Akhir</td>
                        <td class="border px-4 py-3 text-right">{{ number_format($reportData['modal_akhir'] ?? 0, 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-filament::page>
