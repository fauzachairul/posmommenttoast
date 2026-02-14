<div x-data="{ showForm: @entangle('showForm') }">
    <!-- Header & Tombol Tambah Menu -->
    <div class="flex justify-between mb-4">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Menu</h2>
        <div class="relative">
            <button @click="showForm = true" :disabled="showForm"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow flex items-center gap-2">
                <span>+ Tambah Menu</span>
                <svg x-show="showForm" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </button>
        </div>
    </div>

    <!-- Pesan sukses -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('message') }}
        </div>
    @endif

    <!-- Form Tambah/Edit Menu dengan fade-in -->
    <div x-show="showForm" x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4"
        class="bg-white p-6 rounded-xl shadow-lg mb-6 border border-gray-200">
        <h3 class="font-bold text-lg mb-4 text-gray-700">
            {{ $product_id ? 'Edit Menu' : 'Tambah Menu Baru' }}
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
            <div class="col-span-2 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu</label>
                    <input type="text" wire:model="name"
                        class="border p-2 w-full rounded focus:ring-blue-500 focus:border-blue-500">
                    @error('name')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                    <input type="number" wire:model="price"
                        class="border p-2 w-full rounded focus:ring-blue-500 focus:border-blue-500">
                    @error('price')
                        <span class="text-red-500 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="border-l pl-6 overflow-x-hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">Foto Menu</label>
                <div class="mb-3">
                    @if ($image)
                        <img src="{{ $image->temporaryUrl() }}"
                            class="w-32 h-32 object-cover rounded-lg border border-gray-300 shadow-sm">
                        <span class="text-xs text-green-600 block mt-1">Gambar baru terpilih</span>
                    @elseif ($oldImage)
                        <img src="{{ asset('storage/' . $oldImage) }}"
                            class="w-32 h-32 object-cover rounded-lg border shadow-sm">
                        <span class="text-xs text-gray-500 block mt-1">Gambar saat ini</span>
                    @else
                        <div
                            class="w-32 h-32 bg-gray-100 rounded-lg border border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-xs">
                            No Image
                        </div>
                    @endif
                </div>

                <input type="file" wire:model="image"
                    class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <div wire:loading wire:target="image" class="text-xs text-blue-500 mt-1">Uploading...</div>
                @error('image')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200 mb-6">
            <h4 class="font-bold text-sm mb-3 text-orange-800">Resep (Bahan yang digunakan)</h4>
            @foreach ($recipe as $index => $row)
                <div class="flex gap-2 mb-2 items-center">
                    <select wire:model="recipe.{{ $index }}.ingredient_id"
                        class="border p-2 w-1/2 rounded bg-white">
                        <option value="">-- Pilih Bahan --</option>
                        @foreach ($allIngredients as $ing)
                            <option value="{{ $ing->id }}">{{ $ing->name }} (stok: {{ $ing->stock }}
                                {{ $ing->unit }})</option>
                        @endforeach
                    </select>
                    <input type="number" wire:model="recipe.{{ $index }}.quantity_needed" placeholder="Jml"
                        class="border p-2 w-1/4 rounded">
                    <button wire:click="removeIngredientRow({{ $index }})"
                        class="bg-red-100 text-red-600 p-2 rounded hover:bg-red-200">
                        &times;
                    </button>
                </div>
            @endforeach
            <button wire:click="addIngredientRow"
                class="mt-2 text-sm text-blue-700 font-semibold hover:underline flex items-center gap-1">
                <span>+</span> Tambah Bahan Lain
            </button>
            @error('recipe.*.ingredient_id')
                <span class="text-red-500 text-xs block mt-1">Pilih bahan dengan benar.</span>
            @enderror
        </div>

        <div class="flex gap-3">
            <button wire:click="save"
                class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded font-bold shadow transition">
                Simpan
            </button>
            <button @click="showForm = false" wire:click="resetForm"
                class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded font-bold shadow transition">
                Batal
            </button>
        </div>
    </div>

    <!-- Tabel Daftar Menu (tetap sama) -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-4">
        <table class="w-full text-sm text-left">
            <thead class="bg-blue-100 text-gray-700 uppercase font-bold text-xs">
                <tr>
                    <th class="p-4 text-center w-24">No.</th>
                    <th class="p-4 text-center w-24">Foto</th>
                    <th class="p-4">Nama Menu</th>
                    <th class="p-4">Harga</th>
                    <th class="p-4">Resep</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($products as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4 text-center">{{ $loop->iteration }}</td>
                        <td class="p-4">
                            @if ($p->image)
                                <img src="{{ asset('storage/' . $p->image) }}" alt="{{ $p->name }}"
                                    class="w-16 h-16 object-cover rounded-lg shadow border border-gray-300">
                            @else
                                <div
                                    class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center text-gray-400 text-xs">
                                    No Pic
                                </div>
                            @endif
                        </td>

                        <td class="p-4 font-bold text-gray-800 text-base">{{ $p->name }}</td>
                        <td class="p-4 text-orange-600 font-bold">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="p-4">
                            <div class="flex flex-wrap gap-1">
                                @forelse($p->ingredients as $ing)
                                    <span
                                        class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded border border-yellow-200">
                                        {{ $ing->name }}: {{ $ing->pivot->quantity_needed }} {{ $ing->unit }}
                                    </span>
                                @empty
                                    <span class="text-gray-400 text-xs italic">Belum ada resep</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="p-4 text-center">
                            <button wire:click="edit({{ $p->id }})"
                                class="bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200 text-xs font-bold">
                                Edit
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
