<x-filament::widget>
    <x-filament::card>
        <x-slot name="header">📦 Produk dengan Stok Menipis</x-slot>

        @forelse ($this->getStocks() as $stock)
            <div class="py-2 border-b text-sm">
                {{ $stock->product->name }} – Stok: {{ $stock->quantity }} (Min: {{ $stock->minimum_stock }})
            </div>
        @empty
            <div class="text-sm text-gray-500">Semua stok aman saat ini 🎉</div>
        @endforelse
    </x-filament::card>
</x-filament::widget>
