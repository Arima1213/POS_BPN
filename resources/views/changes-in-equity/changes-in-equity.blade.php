<x-filament::page>
    <form wire:submit.prevent="">
        <div class="flex gap-4 mb-4">
            <x-filament::input type="date" wire:model.defer="from" label="Tanggal Awal" />
            <x-filament::input type="date" wire:model.defer="until" label="Tanggal Akhir" />
            <x-filament::button type="submit">Tampilkan</x-filament::button>
        </div>
    </form>

    @php $data = $this->getEquityReport(); @endphp

    <div class="border rounded p-4 shadow">
        <h3 class="text-lg font-bold mb-4">Laporan Perubahan Ekuitas</h3>
        <table class="w-full text-sm border">
            <tr>
                <td class="border px-2 py-1">Modal Awal</td>
                <td class="border px-2 py-1 text-right">{{ number_format($data['modal_awal'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="border px-2 py-1">Laba Bersih</td>
                <td class="border px-2 py-1 text-right">{{ number_format($data['laba_bersih'], 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="border px-2 py-1">Prive</td>
                <td class="border px-2 py-1 text-right">({{ number_format($data['prive'], 2, ',', '.') }})</td>
            </tr>
            <tr class="font-bold">
                <td class="border px-2 py-1">Modal Akhir</td>
                <td class="border px-2 py-1 text-right">{{ number_format($data['modal_akhir'], 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>
</x-filament::page>
