<x-filament::page>
    <form wire:submit.prevent="mount">
        <div class="flex gap-4 mb-4">
            <x-filament::input type="date" wire:model.defer="tanggal" label="Tanggal Neraca" />
            <x-filament::button type="submit">Tampilkan</x-filament::button>
        </div>
    </form>

    @foreach ($this->getBalanceSheetData() as $kategori)
        <div class="mb-6 border rounded p-4 shadow">
            <h2 class="font-bold text-lg mb-3">{{ strtoupper($kategori['kategori']) }}</h2>
            <table class="w-full text-sm border">
                <thead>
                    <tr>
                        <th class="border px-2 py-1 text-left">Akun</th>
                        <th class="border px-2 py-1 text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kategori['rows'] as $row)
                        <tr>
                            <td class="border px-2 py-1">{{ $row['akun']->kode }} - {{ $row['akun']->nama }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="font-bold ">
                        <td class="border px-2 py-1 text-right">Total {{ $kategori['kategori'] }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($kategori['total'], 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
</x-filament::page>
