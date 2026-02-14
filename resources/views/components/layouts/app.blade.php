<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toast POS System</title>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Production version -->
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="bg-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">

        <aside
            class="fixed top-0 left-0 h-full w-64 bg-gray-900 text-white
         -translate-x-full transition-transform duration-300
         flex flex-col z-50 lg:relative
         lg:translate-x-0">
            <div class="p-6 text-center font-bold text-2xl border-b border-gray-700">
                Momment Toast
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('dashboard') }}"
                    class="block p-3 rounded hover:bg-orange-600 {{ request()->routeIs('dashboard') ? 'bg-orange-600' : '' }}">
                    <span class="flex gap-x-1 items-center">
                        <i data-lucide="house" class="w-4 h-4 mr-2"></i> Dashboard </span>
                </a>
                <a href="{{ route('pos') }}"
                    class="block p-3 rounded hover:bg-orange-600 {{ request()->routeIs('pos') ? 'bg-orange-600' : '' }}">
                    <span class="flex gap-x-1 items-center">
                        <i data-lucide="shopping-cart" class="w-4 h-4 mr-2"></i> Kasir (POS)
                    </span>
                </a>
                <a href="{{ route('active.orders') }}"
                    class="block p-3 rounded hover:bg-orange-600 {{ request()->routeIs('active.orders') ? 'bg-orange-600' : '' }}">
                    <span class="flex gap-x-1 items-center">
                        <i data-lucide="inbox" class="w-4 h-4 mr-2"></i> Pesanan Aktif
                    </span>
                </a>
                <a href="{{ route('products') }}"
                    class="block p-3 rounded hover:bg-orange-600 {{ request()->routeIs('products') ? 'bg-orange-600' : '' }}">
                    <span class="flex gap-x-1 items-center">
                        <i data-lucide="package" class="w-4 h-4 mr-2"></i> Produk & Resep
                    </span>
                </a>
                <a href="{{ route('ingredients') }}"
                    class="block p-3 rounded hover:bg-orange-600 {{ request()->routeIs('ingredients') ? 'bg-orange-600' : '' }}">
                    <span class="flex gap-x-1 items-center">
                        <i data-lucide="package" class="w-4 h-4 mr-2"></i> Bahan Baku
                    </span>
                </a>
                <a href="{{ route('stock-in') }}"
                    class="block p-3 rounded hover:bg-orange-600 {{ request()->routeIs('stock-in') ? 'bg-orange-600' : '' }}">
                    <span class="flex gap-x-1 items-center">
                        <i data-lucide="log-in" class="w-4 h-4 mr-2"></i> Stok Masuk
                    </span>
                </a>
                <a href="{{ route('transactions') }}"
                    class="block p-3 rounded hover:bg-orange-600 {{ request()->routeIs('transactions') ? 'bg-orange-600' : '' }}">
                    <span class="flex gap-x-1 items-center">
                        <i data-lucide="credit-card" class="w-4 h-4 mr-2"></i> Riwayat Transaksi
                    </span>
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700 text-sm text-gray-400 w-full">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="p-3 bg-orange-600 block w-full rounded text-white font-medium hover:bg-orange-700 transition duration-200">
                        <span class="flex gap-x-1 items-center"><i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
                            Logout</span>
                    </button>
                </form>
                {{-- <a href="{{ route('logout') }}"
                    class="p-3 bg-orange-600 block w-full rounded text-white font-medium hover:bg-orange-700 transition duration-200">
                    <span class="flex gap-x-1 items-center"><i data-lucide="log-out" class="w-4 h-4 mr-2"></i>
                        Logout</a></span> --}}
            </div>
        </aside>

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            <div class="p-4 bg-white shadow mb-5 flex justify-end items-center space-x-4 lg:hidden">
                <button id="btn-toggle"
                    class="p-2 bg-gray-100 rounded shadow active:shadow-md active:bg-gray-200 transition duration-200"><i
                        data-lucide="menu"></i>
                </button>
            </div>
            {{ $slot }}
        </main>
    </div>

    @vite('resources/js/app.js')
    @livewireScripts

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        const btnToggle = document.getElementById('btn-toggle');
        const sidebar = document.querySelector('aside');

        btnToggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });
    </script>
</body>

</html>
