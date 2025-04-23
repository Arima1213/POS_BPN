<x-filament::page>
    <form wire:submit.prevent="getTrialBalanceData">
        <div class="flex gap-4 mb-4">
            <x-filament::input type="date" wire:model.defer="from" label="Tanggal Awal" />
            <x-filament::input type="date" wire:model.defer="until" label="Tanggal Akhir" />
            <x-filament::button type="submit">Tampilkan</x-filament::button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm border rounded-sm">
            <thead class="">
                <tr>
                    <th class="border px-2 py-1 text-left">Kode Akun</th>
                    <th class="border px-2 py-1 text-left">Nama Akun</th>
                    <th class="border px-2 py-1 text-right">Debit</th>
                    <th class="border px-2 py-1 text-right">Kredit</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalDebit = 0;
                    $totalKredit = 0;
                @endphp
                @foreach ($this->getTrialBalanceData() as $row)
                    @php
                        $totalDebit += $row['debit'];
                        $totalKredit += $row['kredit'];
                    @endphp
                    <tr>
                        <td class="border px-2 py-1">{{ $row['akun']->kode }}</td>
                        <td class="border px-2 py-1">{{ $row['akun']->nama }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($row['debit'], 2, ',', '.') }}</td>
                        <td class="border px-2 py-1 text-right">{{ number_format($row['kredit'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="font-bold ">
                    <td colspan="2" class="text-right px-2 py-1">Total</td>
                    <td class="text-right px-2 py-1">{{ number_format($totalDebit, 2, ',', '.') }}</td>
                    <td class="text-right px-2 py-1">{{ number_format($totalKredit, 2, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</x-filament::page>
