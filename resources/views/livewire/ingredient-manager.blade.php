<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded shadow h-fit">
        <h3 class="font-bold mb-4 text-lg">{{ $isEdit ? 'Edit Bahan' : 'Tambah Bahan Baru' }}</h3>
        <form wire:submit.prevent="store">
            <div class="mb-3">
                <label class="block text-sm">Nama Bahan</label>
                <input type="text" wire:model="name" class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div class="mb-3">
                <label class="block text-sm">Stok Awal</label>
                <input type="number" wire:model="stock" class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div class="mb-3">
                <label class="block text-sm">Satuan (gr, ml, pcs)</label>
                <input type="text" wire:model="unit" class="w-full border border-gray-300 p-2 rounded">
            </div>
            <div class="flex gap-2">
                <button type="submit" wire:loading.attr="disabled" wire:target="store"
                    class="bg-slate-800 text-white px-4 py-2 rounded w-full flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                    <!-- Normal -->
                    <span wire:loading.remove wire:target="store">
                        Simpan
                    </span>

                    <!-- Loading -->
                    <div wire:loading wire:target="store" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        <span>
                            Menyimpan...
                        </span>
                    </div>
                </button>

                @if ($isEdit)
                    <button type="button" wire:click="resetInput"
                        class="bg-gray-300 text-slate-800 px-4 py-2 rounded">Batal</button>
                @endif
            </div>
        </form>
    </div>

    <div class="md:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-gray-50 border-b border-gray-100 shadow">
                    <tr>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wide">NO.</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wide">Nama</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wide">Stok</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wide">Satuan</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wide text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($ingredients as $item)
                        <tr class="odd:bg-white even:bg-gray-50 hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ $item->name }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                                    {{ $item->stock }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $item->unit }}
                            </td>

                            <td class="px-6 py-4 text-center space-x-2">
                                <button wire:click="edit({{ $item->id }})"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg 
                                       bg-blue-600 text-white hover:bg-blue-700 transition">
                                    Edit
                                </button>

                                <button wire:click="delete({{ $item->id }})"
                                    onclick="confirm('Hapus data ini?') || event.stopImmediatePropagation()"
                                    class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg 
                                       bg-red-600 text-white hover:bg-red-700 transition">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
