<div>
    <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
        <i data-lucide="timer" class="w-5 h-5 mr-2"></i> Transaksi Aktif / Dapur
        <span class="text-sm font-normal text-gray-500 bg-gray-200 px-2 py-1 rounded-full">{{ $orders->count() }}
            Antrian</span>
    </h2>

    @if (session()->has('message'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($orders as $order)
            <div
                class="bg-white rounded-xl shadow-lg overflow-hidden relative hover:ring-orange-400 hover:ring-2 transition">

                <div class="bg-gray-50 p-4 border-b border-gray-200 shadow flex justify-between items-start">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800">{{ $order->customer_name }}</h3>
                        <span class="text-xs font-mono bg-orange-100 text-orange-800 px-2 py-1 rounded">
                            {{ $order->transaction_code }}
                        </span>
                    </div>
                    <div class="text-right text-xs text-gray-500">
                        {{ $order->created_at->diffForHumans() }}<br>
                        {{ $order->created_at->format('H:i') }}
                    </div>
                </div>

                <div class="p-4 space-y-3 min-h-37.5">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between items-center border-b border-dashed pb-2 last:border-0">
                            <div class="flex items-center gap-2">
                                <div class="bg-gray-200 rounded px-2 py-1 font-bold text-gray-700 text-sm">
                                    {{ $item->quantity }}x
                                </div>
                                <span class="text-gray-800 font-medium">{{ $item->product->name }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-gray-100 p-4 flex gap-2">
                    <!-- Tombol Batal -->
                    <button wire:click="markAsCancelled({{ $order->id }})" wire:loading.attr="disabled"
                        wire:target="markAsCancelled({{ $order->id }})"
                        wire:confirm="Yakin ingin membatalkan pesanan ini?"
                        class="flex-1 bg-red-100 text-red-600 py-3 rounded-lg font-medium hover:bg-red-200 transition flex items-center justify-center shadow">

                        <!-- Spinner -->
                        <svg wire:loading wire:target="markAsCancelled({{ $order->id }})"
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-red-600" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                        <i data-lucide="x-circle" class="w-4 h-4 mr-2"></i> BATAL
                    </button>

                    <!-- Tombol Done & Print -->
                    <button wire:click="markAsDone({{ $order->id }})" wire:loading.attr="disabled"
                        wire:target="markAsDone({{ $order->id }})"
                        class="flex-1 bg-green-600 text-white py-3 rounded-lg font-medium hover:bg-green-700 transition shadow-lg flex items-center justify-center">

                        <!-- Spinner -->
                        <svg wire:loading wire:target="markAsDone({{ $order->id }})"
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>

                        <i data-lucide="check-circle" class="w-4 h-4 mr-2"></i> DONE & PRINT
                    </button>
                </div>

            </div>
        @empty
            <div class="col-span-3 text-center py-20 bg-gray-50 rounded-lg border-2 border-dashed">
                <p class="text-gray-400 text-xl font-bold">Tidak ada pesanan aktif</p>
                <p class="text-gray-400 text-sm">Pesanan baru dari kasir akan muncul di sini</p>
            </div>
        @endforelse
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('print-receipt', (event) => {
            const orderId = event.orderId;
            const win = window.open(`/print-receipt/${orderId}`, '_blank', 'width=400,height=600');
        });
    });
</script>
