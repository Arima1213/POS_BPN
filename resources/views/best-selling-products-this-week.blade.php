<x-filament::widget>
    <x-filament::card>
        <x-slot name="header">
            📦 Produk Terlaris Minggu Ini
        </x-slot>

        {{-- @dd($this->getBestSellingProductsThisWeek()) --}}
        @forelse ($this->getBestSellingProductsThisWeek() as $products)
            <ul class="divide-y divide-gray-200">
                @dd($products)
                @foreach ($products as $product)
                    <li class="py-3 flex justify-between items-center">
                        <div class="flex items-center space-x-3">
                            @if ($product->item?->image)
                                <img src="{{ asset('storage/' . $product->item->image) }}" alt="{{ $product->item->name }}" class="w-10 h-10 rounded-md object-cover">
                            @else
                                <div class="w-10 h-10 rounded-md bg-gray-200 flex items-center justify-center text-gray-500">
                                    📦
                                </div>
                            @endif
                            <div>
                                <div class="font-medium">{{ $product->item?->name ?? 'Produk tidak ditemukan' }}</div>
                                @if ($product->item?->category?->name)
                                    <div class="text-xs text-gray-400">{{ $product->item->category->name }}</div>
                                @endif
                                <div class="text-xs text-gray-500 mt-0.5">Terjual: {{ $product->total_sold }}</div>
                            </div>
                        </div>
                        <span class="text-sm font-semibold text-primary-600">
                            {{ number_format($product->total_sold) }} pcs
                        </span>
                    </li>
                @endforeach
            </ul>
        @empty
            <div class="text-gray-500 text-sm text-center py-8">
                Tidak ada data penjualan minggu ini.
            </div>
        @endforelse

    </x-filament::card>
</x-filament::widget>
