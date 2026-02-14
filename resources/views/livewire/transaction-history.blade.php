<div>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h1>
            <p class="text-sm text-gray-500">Pantau penjualan harian Anda</p>
        </div>

        <div class="flex items-center gap-2 bg-white p-2 rounded shadow">
            <span class="text-sm font-bold text-gray-600">Filter Tgl:</span>
            <input type="date" wire:model.live="dateFilter" class="border rounded p-1 text-sm">
        </div>
    </div>

    <div class="bg-amber-500 text-white p-4 rounded-lg shadow-lg mb-6 flex justify-between items-center">
        <div>
            <p class="text-sm opacity-80">
                {{ $dateFilter ? 'Total Pendapatan Tanggal Ini' : 'Total Pendapatan Keseluruhan' }}
            </p>
            <h2 class="text-3xl font-bold">
                Rp {{ number_format($dailyTotal, 0, ',', '.') }}
            </h2>

        </div>
        <div class="text-4xl opacity-50">💰</div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-blue-100 text-gray-700 uppercase font-bold text-xs">
                <tr>
                    <th class="p-4">No</th>
                    <th class="p-4">Kode Transaksi</th>
                    <th class="p-4">Jam</th>
                    <th class="p-4">Nama Pelanggan</th>
                    <th class="p-4 text-right">Total Bayar</th>
                    <th class="p-4 text-center">Status</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 font-mono text-gray-500">{{ $loop->iteration }}</td>
                        <td class="p-4 font-mono text-gray-500">{{ $order->transaction_code ?? '-' }}</td>
                        <td class="p-4">
                            <div class="text-sm font-semibold text-gray-700">
                                {{ $order->transaction_date->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $order->transaction_date->format('H:i') }}
                            </div>
                        </td>

                        <td class="p-4 font-bold">{{ $order->customer_name }}</td>
                        <td class="p-4 text-right font-bold text-orange-600">
                            Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                        </td>
                        <td class="p-4 text-center">
                            <span
                                class="px-2 py-1 rounded-full text-xs font-bold {{ $order->status == 'completed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="p-4 font-mono text-gray-500">
                            <button wire:click="showDetail({{ $order->id }})"
                                class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold hover:bg-blue-200 transition">
                                Lihat Detail
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">
                            Tidak ada transaksi pada tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 bg-gray-50 border-t border-gray-300">
            {{ $orders->links() }}
        </div>
    </div>

    @if ($isShowModal && $selectedOrder)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 backdrop-blur-lg p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate-fade-in-up">

                <div class="bg-gray-100 p-4 border-b flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg">Detail Order #{{ $selectedOrder->transaction_code }}</h3>
                        <p class="text-xs text-gray-500">{{ $selectedOrder->transaction_date->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <button wire:click="closeModal"
                        class="text-gray-400 hover:text-red-500 text-2xl font-bold">&times;</button>
                </div>

                <div class="p-4 max-h-[60vh] overflow-y-auto">
                    <div class="mb-4 pb-4 border-b">
                        <p class="text-sm text-gray-500">Pelanggan</p>
                        <p class="font-bold text-lg">{{ $selectedOrder->customer_name }}</p>
                    </div>

                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-gray-500 text-xs border-b">
                                <th class="text-left pb-2">Menu</th>
                                <th class="text-center pb-2">Qty</th>
                                <th class="text-right pb-2">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($selectedOrder->items as $item)
                                <tr>
                                    <td class="py-3">
                                        <span
                                            class="font-bold block text-gray-800">{{ $item->product->name ?? 'Produk Dihapus' }}</span>
                                        <span class="text-xs text-gray-500">@ Rp
                                            {{ number_format($item->price_at_purchase) }}</span>
                                    </td>
                                    <td class="py-3 text-center font-bold">x{{ $item->quantity }}</td>
                                    <td class="py-3 text-right font-bold">Rp {{ number_format($item->subtotal) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="bg-gray-50 p-4 border-t flex justify-between items-center">
                    <span class="text-gray-600 font-bold">TOTAL BAYAR</span>
                    <span class="text-xl font-bold text-orange-600">Rp
                        {{ number_format($selectedOrder->total_amount, 0, ',', '.') }}</span>
                </div>

                <div class="p-2 text-center bg-white border-t">
                    <button
                        onclick="window.open('/print-receipt/{{ $selectedOrder->id }}', '_blank', 'width=400,height=600')"
                        class="text-blue-600 hover:underline text-sm font-bold">
                        Cetak Ulang Struk
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
