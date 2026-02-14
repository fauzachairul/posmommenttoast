<div>
    <h1 class="text-3xl font-bold mb-6">Dashboard Overview</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

        {{-- Total Pendapatan --}}
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-emerald-500">
            <h3 class="text-gray-500 text-sm">Total Pendapatan</h3>
            <p class="text-3xl font-bold">Rp {{ number_format($totalSales) }}</p>
        </div>

        {{-- Pendapatan Bulan Ini --}}
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-indigo-500">
            <h3 class="text-gray-500 text-sm">Pendapatan Bulan Ini</h3>
            <p class="text-3xl font-bold">Rp {{ number_format($monthlySales) }}</p>
        </div>

        {{-- Omset Hari Ini --}}
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
            <h3 class="text-gray-500 text-sm">Omset Hari Ini</h3>
            <p class="text-3xl font-bold">Rp {{ number_format($todaySales) }}</p>
        </div>

        {{-- Transaksi Hari Ini --}}
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
            <h3 class="text-gray-500 text-sm">Transaksi Hari Ini</h3>
            <p class="text-3xl font-bold">{{ $todayCount }}</p>
        </div>

        {{-- Total Pesanan --}}
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
            <h3 class="text-gray-500 text-sm">Total Order Selesai</h3>
            <p class="text-3xl font-bold">{{ $totalOrders }}</p>
        </div>

        {{-- stok kritis --}}
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-500">
            <h3 class="text-gray-500 text-sm">Bahan Baku Kritis</h3>
            <p class="text-3xl font-bold">{{ $lowStock->count() }} Item</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-lg text-gray-700">5 Menu Paling Laris</h3>
                <span class="text-xs text-gray-500">Berdasarkan Item Terjual</span>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($bestSellers as $index => $item)
                    <div class="flex items-center p-4 hover:bg-gray-50 transition">
                        <div
                            class="w-8 text-center font-bold text-lg {{ $index == 0 ? 'text-yellow-500' : ($index == 1 ? 'text-gray-400' : ($index == 2 ? 'text-orange-400' : 'text-gray-300')) }}">
                            #{{ $index + 1 }}
                        </div>

                        <div class="w-12 h-12 shrink-0 mx-3">
                            @if ($item->product->image)
                                <img src="{{ asset('storage/' . $item->product->image) }}"
                                    class="w-full h-full object-cover rounded-lg shadow-sm">
                            @else
                                <div
                                    class="w-full h-full bg-gray-200 rounded-lg flex items-center justify-center text-xs text-gray-400">
                                    No Pic</div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">
                                {{ $item->product->name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Harga: Rp {{ number_format($item->product->price, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="text-right">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ $item->total_sold }} Terjual
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-400">
                        Belum ada data penjualan.
                    </div>
                @endforelse
            </div>
        </div>

        @if ($lowStock->count() > 0)
            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <h3 class="font-bold text-red-700 mb-2">⚠️ Peringatan Stok Menipis</h3>
                <ul class="list-disc list-inside text-red-600">
                    @foreach ($lowStock as $item)
                        <li>{{ $item->name }} (Sisa: {{ $item->stock }} {{ $item->unit }})</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    <div class="mt-8 bg-white p-6 rounded-lg shadow-md mb-8">
        <h3 class="font-bold text-lg text-gray-700 mb-4">Statistik Penjualan 7 Hari Terakhir</h3>
        <div class="relative h-80 w-full">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('revenueChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar', // Tipe utama
                data: {
                    labels: @json($chartLabels),
                    datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: @json($chartRevenue),
                            backgroundColor: 'rgba(249, 115, 22, 0.5)', // Orange transparan
                            borderColor: 'rgba(249, 115, 22, 1)', // Orange solid
                            borderWidth: 1,
                            yAxisID: 'y', // Sumbu Y Kiri
                            order: 2
                        },
                        {
                            label: 'Jumlah Order',
                            data: @json($chartOrders),
                            type: 'line', // Override tipe jadi garis
                            borderColor: 'rgba(37, 99, 235, 1)', // Biru
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#fff',
                            pointBorderColor: 'rgba(37, 99, 235, 1)',
                            pointRadius: 5,
                            tension: 0.3, // Garis melengkung halus
                            yAxisID: 'y1', // Sumbu Y Kanan
                            order: 1
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.dataset.yAxisID === 'y') {
                                        // Format Rupiah di Tooltip
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR'
                                        }).format(context.raw);
                                    } else {
                                        label += context.raw;
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Rupiah (Rp)'
                            },
                            beginAtZero: true
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Jumlah Order'
                            },
                            grid: {
                                drawOnChartArea: false, // Hilangkan grid untuk sumbu kanan agar rapi
                            },
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1 // Agar sumbu order selalu bilangan bulat
                            }
                        },
                    }
                }
            });
        });
    </script>
