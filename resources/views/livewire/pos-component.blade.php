<div class="flex min-h-screen bg-gray-100 flex-col lg:flex-row gap-6 p-6">
    <!-- Menu List -->
    <div class="w-full lg:w-2/3 p-6 lg:overflow-y-auto">
        <h2 class="text-2xl font-bold mb-4 text-orange-600">Toast Menu</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($products as $product)
                <div wire:click="addToCart({{ $product->id }})"
                    class="bg-white p-4 rounded-xl shadow cursor-pointer hover:ring-2 hover:ring-orange-400 transition">
                    <img src="{{ asset('storage/' . $product->image) }}"
                        class="w-full h-32 object-cover rounded mb-2 shadow" alt="Menu">
                    <h3 class="font-bold text-gray-800 mt-2">{{ $product->name }}</h3>
                    <p class="text-orange-500 font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Cart -->
    <div class="w-full lg:w-1/3 max-h-[90vh] rounded bg-white p-6 shadow-xl flex flex-col justify-between">
        <div>
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Detail Pesanan</h2>

            <div class="mb-4 space-y-2">
                <div>
                    <label class="text-xs text-gray-500">Tanggal Transaksi</label>
                    <input type="datetime-local" wire:model="transactionDate"
                        class="w-full border border-gray-300 p-1 rounded bg-gray-50">
                </div>
                <div>
                    <label class="text-xs text-gray-500">Nama Pembeli</label>
                    <input type="text" wire:model="customerName" placeholder="Masukkan nama..."
                        class="w-full border border-gray-300 p-2 rounded focus:ring-orange-500">
                    @error('customerName')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="h-64 overflow-y-auto space-y-3 mb-4">
                @forelse($cart as $item)
                    <div class="flex items-center justify-between border-b pb-2">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-gray-200 rounded overflow-hidden">
                                @if ($item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}"
                                        class="object-cover w-full h-full">
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold truncate w-32">{{ $item['name'] }}</p>
                                <p class="text-xs text-gray-500">@ Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button wire:click="updateQty({{ $item['id'] }}, 'minus')"
                                class="w-6 h-6 bg-gray-200 rounded hover:bg-red-200">-</button>
                            <span class="font-bold text-sm w-4 text-center">{{ $item['qty'] }}</span>
                            <button wire:click="updateQty({{ $item['id'] }}, 'plus')"
                                class="w-6 h-6 bg-gray-200 rounded hover:bg-green-200">+</button>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 mt-10">Belum ada menu dipilih</div>
                @endforelse
            </div>
        </div>

        <div class="border-t pt-4">
            <div class="flex justify-between text-xl font-bold mb-4">
                <span>Total</span>
                <span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>

            <button wire:click="checkout" wire:loading.attr="disabled"
                class="w-full bg-orange-600 text-white py-3 rounded-lg font-bold hover:bg-orange-700 transition">
                <span wire:loading.remove>Order</span>
                <span wire:loading>Memproses...</span>
            </button>

            @if (session()->has('message'))
                <div class="mt-2 text-green-600 text-center text-sm">{{ session('message') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="mt-2 text-red-600 text-center text-sm">{{ session('error') }}</div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('print-receipt', (event) => {
            const orderId = event.orderId;
            const printUrl = `/print-receipt/${orderId}`;
            const win = window.open(printUrl, '_blank', 'width=400,height=600');
            win.focus();
        });
    });
</script>
