<div class="space-y-4">
    @forelse ($histories as $history)
        <div class="border rounded p-3 shadow-sm">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-sm font-semibold">
                        {{ $history->type === 'in' ? 'in' : 'out' }}
                        ({{ $history->type === 'in' ? '+' : '-' }}{{ abs($history->quantity) }})
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ $history->created_at->diffForHumans() }}
                    </div>
                </div>
                <div class="text-xs text-right text-gray-400">
                    {{ $history->note }}
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-gray-400">
            No history available.
        </div>
    @endforelse
</div>
