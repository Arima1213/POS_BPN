<x-filament::page>
    <form wire:submit.prevent="getBalanceSheetData">
        <div class="flex gap-4 mb-4">
            <x-filament::input type="date" wire:model.defer="from" label="Tanggal Awal" />
            <x-filament::input type="date" wire:model.defer="until" label="Tanggal Akhir" />
            <x-filament::button type="submit">Tampilkan</x-filament::button>
            <x-filament::button tag="a" href="{{ route('balance-sheet.export.pdf', ['from' => $this->from, 'until' => $this->until]) }}" target="_blank">
            PDF
            </x-filament::button>
        </div>
    </form>

    @foreach ($this->getBalanceSheetData() as $kelompok)
        <div class="mb-6 border rounded p-4 shadow">
            <h2 class="font-bold text-lg mb-3">{{ strtoupper($kelompok['kelompok']) }}</h2>
            <table class="w-full text-sm border">
                <thead>
                    <tr>
                        <th class="border px-2 py-1 text-left">Akun</th>
                        <th class="border px-2 py-1 text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kelompok['rows'] as $row)
                        <tr>
                            <td class="border px-2 py-1">{{ $row['akun']->kode }} - {{ $row['akun']->nama }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="font-bold">
                        <td class="border px-2 py-1 text-right">Total {{ ucfirst($kelompok['kelompok']) }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($kelompok['total'], 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
</x-filament::page>
