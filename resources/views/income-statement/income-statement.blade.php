<x-filament::page>
    <form wire:submit.prevent="tampilkan">
        <div class="flex gap-4 mb-4">
            <x-filament::input type="date" wire:model.defer="from" label="Tanggal Awal" />
            <x-filament::input type="date" wire:model.defer="until" label="Tanggal Akhir" />
            <x-filament::button type="submit">Tampilkan</x-filament::button>
            <x-filament::button tag="a" href="{{ route('owner.income-statement.download.pdf', ['from' => $this->from, 'until' => $this->until]) }}" target="_blank">
            PDF
            </x-filament::button>
        </div>
    </form>

    <div class="grid md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-lg font-semibold mb-2">Pendapatan</h2>
            <ul class="border rounded p-2">
                @foreach ($pendapatan as $item)
                    <li class="flex justify-between border-b py-1">
                        <span>{{ $item['akun']->nama }}</span>
                        <span>{{ number_format($item['total'], 2, ',', '.') }}</span>
                    </li>
                @endforeach
                <li class="flex justify-between font-bold py-2">
                    <span>Total Pendapatan</span>
                    <span>{{ number_format($totalPendapatan, 2, ',', '.') }}</span>
                </li>
            </ul>
        </div>

        <div>
            <h2 class="text-lg font-semibold mb-2">Biaya</h2>
            <ul class="border rounded p-2">
                @foreach ($biaya as $item)
                    <li class="flex justify-between border-b py-1">
                        <span>{{ $item['akun']->nama }}</span>
                        <span>{{ number_format($item['total'], 2, ',', '.') }}</span>
                    </li>
                @endforeach
                <li class="flex justify-between font-bold py-2">
                    <span>Total Biaya</span>
                    <span>{{ number_format($totalBiaya, 2, ',', '.') }}</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="mt-6 p-4 border-t text-right font-bold text-xl">
        Laba Bersih: {{ number_format($labaBersih, 2, ',', '.') }}
    </div>
</x-filament::page>
