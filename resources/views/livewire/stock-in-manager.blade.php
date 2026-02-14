<div class="space-y-8">

    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Stok Masuk (Belanja Bahan)</h1>
        @if (session('message'))
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded shadow animate-pulse">
                {{ session('message') }}
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-lg shadow lg:col-span-1 border-t-4 border-blue-500">
            <h3 class="font-bold mb-4 text-lg">1. Data Belanja</h3>

            <div class="space-y-4">
                <div>
                    <label class="text-xs font-bold text-gray-500">Tanggal</label>
                    <input type="date" wire:model="transactionDate" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="text-xs font-bold text-gray-500">Supplier / Toko</label>
                    <input type="text" wire:model="supplierName" class="w-full border p-2 rounded"
                        placeholder="Contoh: Agen Telur Pak Budi">
                </div>

                <hr class="border-dashed">

                <div>
                    <label class="text-xs font-bold text-gray-500">Pilih Bahan Baku</label>
                    <select wire:model="selectedIngredientId" class="w-full border p-2 rounded bg-gray-50">
                        <option value="">-- Pilih --</option>
                        @foreach ($ingredients as $ing)
                            <option value="{{ $ing->id }}">{{ $ing->name }} (Satuan: {{ $ing->unit }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-2">
                    <div class="w-1/2">
                        <label class="text-xs font-bold text-gray-500">Jumlah Beli</label>
                        <input type="number" wire:model="inputQty" class="w-full border p-2 rounded" min="1">
                    </div>
                    <div class="w-1/2">
                        <label class="text-xs font-bold text-gray-500">Harga Satuan (Rp)</label>
                        <input type="number" wire:model="inputCost" class="w-full border p-2 rounded" min="0">
                    </div>
                </div>

                <button wire:click="addItem"
                    class="w-full bg-slate-800 text-white py-2 rounded font-medium hover:bg-slate-900 transition">
                    + Tambah ke List
                </button>
            </div>
        </div>

        <div
            class="bg-white p-6 rounded-lg shadow lg:col-span-2 flex flex-col justify-between border-t-4 border-blue-500">
            <div>
                <h3 class="font-bold mb-4 text-lg">2. Daftar Barang Masuk</h3>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2">Nama Bahan</th>
                            <th class="p-2 text-center">Qty</th>
                            <th class="p-2 text-right">Harga Beli</th>
                            <th class="p-2 text-right">Subtotal</th>
                            <th class="p-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @php $grandTotal = 0; @endphp
                        @forelse($cart as $id => $item)
                            @php
                                $subtotal = $item['qty'] * $item['cost'];
                                $grandTotal += $subtotal;
                            @endphp
                            <tr>
                                <td class="p-2 font-bold">{{ $item['name'] }}</td>
                                <td class="p-2 text-center">{{ $item['qty'] }} {{ $item['unit'] }}</td>
                                <td class="p-2 text-right">Rp {{ number_format($item['cost']) }}</td>
                                <td class="p-2 text-right font-bold">Rp {{ number_format($subtotal) }}</td>
                                <td class="p-2 text-center">
                                    <button wire:click="removeItem({{ $id }})"
                                        class="text-red-500 hover:text-red-700 font-bold">&times;</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">Belum ada barang dipilih.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 border-t pt-4">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600 font-bold text-lg">Total Belanja</span>
                    <span class="text-2xl font-bold text-blue-700">Rp {{ number_format($grandTotal) }}</span>
                </div>
                <button wire:click="saveSupply" @if (empty($cart)) disabled @endif
                    class="w-full bg-green-600 text-white py-3 rounded-lg font-bold text-lg hover:bg-green-700 disabled:bg-gray-300 transition">
                    SIMPAN STOK MASUK
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow border-t-4 border-gray-500">
        <h3 class="font-bold mb-4 text-xl border-b pb-2">Riwayat Stok Masuk</h3>

        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-bold">
                <tr>
                    <th class="p-3 text-left">Kode TRX</th>
                    <th class="p-3 text-left">Tanggal</th>
                    <th class="p-3 text-left">Supplier</th>
                    <th class="p-3 text-left">Detail Barang</th>
                    <th class="p-3 text-right">Total Biaya</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach ($supplies as $supply)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-mono font-bold">{{ $supply->transaction_code }}</td>
                        <td class="p-3">{{ $supply->transaction_date->format('d M Y') }}</td>
                        <td class="p-3">{{ $supply->supplier_name }}</td>
                        <td class="p-3">
                            <ul class="list-disc list-inside text-xs text-gray-600">
                                @foreach ($supply->items as $item)
                                    <li>
                                        <span class="font-bold text-gray-800">{{ $item->ingredient->name }}</span>:
                                        {{ $item->quantity }} {{ $item->ingredient->unit }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="p-3 text-right font-bold text-blue-700">Rp {{ number_format($supply->total_cost) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $supplies->links() }}
        </div>
    </div>
</div>
