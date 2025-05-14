<x-filament::page>
    <form wire:submit.prevent="tampilkan" class="space-y-4 mb-6">
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
                    href="{{ route('owner.income-statement.download.pdf', ['from' => $this->from, 'until' => $this->until]) }}"
                    target="_blank"
                    color="gray"
                    class="w-full md:w-auto"
                >
                    PDF
                </x-filament::button>
            </div>
        </div>
    </form>

    <div class="overflow-x-auto w-full rounded-xl shadow ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-900 p-4">
        <table class="w-full text-sm text-gray-800 dark:text-gray-100 table-auto">
            <thead class="bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-100 border-b border-gray-300 dark:border-gray-700">
                <tr class="text-sm uppercase tracking-wide">
                    <th class="px-4 py-2 text-left">Kategori</th>
                    <th class="px-4 py-2 text-left">Nama Akun</th>
                    <th class="px-4 py-2 text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                {{-- Pendapatan --}}
                @foreach ($pendapatan as $item)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-2">Pendapatan</td>
                        <td class="px-4 py-2">{{ $item['akun']->nama }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($item['total'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="font-semibold bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-600">
                    <td colspan="2" class="px-4 py-3 text-right">Total Pendapatan</td>
                    <td class="px-4 py-3 text-right">{{ number_format($totalPendapatan, 2, ',', '.') }}</td>
                </tr>
                {{-- Biaya --}}
                @foreach ($biaya as $item)
                    <tr class=" border-b border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-2">Biaya</td>
                        <td class="px-4 py-2">{{ $item['akun']->nama }}</td>
                        <td class="px-4 py-2 text-right">{{ number_format($item['total'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="font-semibold bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white border-t-2 border-gray-300 dark:border-gray-600">
                    <td colspan="2" class="px-4 py-3 text-right">Total Biaya</td>
                    <td class="px-4 py-3 text-right">{{ number_format($totalBiaya, 2, ',', '.') }}</td>
                </tr>
                {{-- Laba Bersih --}}
                <tr class="font-bold text-xl bg-green-50 dark:bg-green-900 text-green-800 dark:text-green-200 border-t-2 border-green-300 dark:border-green-600">
                    <td colspan="2" class="px-4 py-3 text-right">Laba Bersih</td>
                    <td class="px-4 py-3 text-right">{{ number_format($labaBersih, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</x-filament::page>
