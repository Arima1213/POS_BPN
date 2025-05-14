<x-filament::page>
    <form wire:submit.prevent="getBalanceSheetData" class="space-y-4 mb-6">
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
                    href="{{ route('balance-sheet.export.pdf', ['from' => $this->from, 'until' => $this->until]) }}"
                    target="_blank"
                    color="gray"
                    class="w-full md:w-auto"
                >
                    PDF
                </x-filament::button>
            </div>
        </div>
    </form>

    @foreach ($this->getBalanceSheetData() as $kelompok)
        <div class="mb-8">
            <div class="rounded-xl shadow ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900 p-4">
                <h2 class="font-bold text-lg mb-4 text-gray-800 dark:text-gray-100">
                    {{ strtoupper($kelompok['kelompok']) }}
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-gray-800 dark:text-gray-100 table-auto">
                        <thead class="bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-100 border-b border-gray-300 dark:border-gray-700">
                            <tr class="text-sm uppercase tracking-wide">
                                <th class="px-4 py-2 text-left">Akun</th>
                                <th class="px-4 py-2 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kelompok['rows'] as $row)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-4 py-2">{{ $row['akun']->kode }} - {{ $row['akun']->nama }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-semibold bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-600">
                                <td class="px-4 py-3 text-right">Total {{ ucfirst($kelompok['kelompok']) }}</td>
                                <td class="px-4 py-3 text-right">{{ number_format($kelompok['total'], 2, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
</x-filament::page>
