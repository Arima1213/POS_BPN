<x-filament::page>
    <form wire:submit.prevent="show">
        <div class="flex gap-4 mb-4">
            <x-filament::input type="date" wire:model.defer="from" label="Tanggal Awal" />
            <x-filament::input type="date" wire:model.defer="until" label="Tanggal Akhir" />
            <x-filament::button type="submit">Tampilkan</x-filament::button>
        </div>
    </form>

    @foreach ($this->getGeneralLedgerData() as $akun)
        <div class="mb-8 border rounded shadow p-4 ">
            <h3 class="font-bold text-lg mb-2">
                ({{ $akun['akun']->kode }}) {{ $akun['akun']->nama }}
            </h3>
            <table class="w-full text-sm border">
                <thead class="">
                    <tr>
                        <th class="border px-2 py-1">Tanggal</th>
                        <th class="border px-2 py-1">Transaksi</th>
                        <th class="border px-2 py-1">Nomor</th>
                        <th class="border px-2 py-1">Keterangan</th>
                        <th class="border px-2 py-1 text-right">Debit</th>
                        <th class="border px-2 py-1 text-right">Kredit</th>
                        <th class="border px-2 py-1 text-right">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($akun['rows'] as $row)
                        <tr>
                            <td class="border px-2 py-1">{{ $row['tanggal'] }}</td>
                            <td class="border px-2 py-1">{{ $row['transaksi'] }}</td>
                            <td class="border px-2 py-1">{{ $row['nomor'] }}</td>
                            <td class="border px-2 py-1">{{ $row['keterangan'] }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($row['debit'], 2, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($row['kredit'], 2, ',', '.') }}</td>
                            <td class="border px-2 py-1 text-right">{{ number_format($row['saldo'], 2, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="font-bold ">
                        <td colspan="6" class="text-right px-2 py-1">Saldo Akhir</td>
                        <td class="text-right px-2 py-1">{{ number_format($akun['saldo_akhir'], 2, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endforeach
</x-filament::page>
