<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function render()
    {
        /**
         * =========================================================
         * 1. CHART 7 HARI TERAKHIR (1 QUERY)
         * =========================================================
         */
        $startDate = Carbon::today()->subDays(6);
        $endDate   = Carbon::today();

        $ordersPerDay = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->selectRaw('
                DATE(created_at) as date,
                SUM(total_amount) as total_omset,
                COUNT(*) as total_trx
            ')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $chartLabels  = [];
        $chartRevenue = [];
        $chartOrders  = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->toDateString();
            $data = $ordersPerDay[$date] ?? null;

            $chartLabels[]  = Carbon::parse($date)->format('d/M');
            $chartRevenue[] = $data->total_omset ?? 0;
            $chartOrders[]  = $data->total_trx ?? 0;
        }

        /**
         * =========================================================
         * 2. STATISTIK ORDER (1 QUERY TOTAL)
         * =========================================================
         */
        $stats = Order::where('status', 'completed')
            ->selectRaw('
                SUM(total_amount) as total_sales,
                COUNT(*) as total_orders,

                SUM(CASE 
                    WHEN DATE(created_at) = CURDATE() 
                    THEN total_amount ELSE 0 
                END) as today_sales,

                SUM(CASE 
                    WHEN DATE(created_at) = CURDATE() 
                    THEN 1 ELSE 0 
                END) as today_count,

                SUM(CASE 
                    WHEN MONTH(created_at) = MONTH(CURDATE()) 
                    AND YEAR(created_at) = YEAR(CURDATE())
                    THEN total_amount ELSE 0 
                END) as monthly_sales
            ')
            ->first();

        /**
         * =========================================================
         * 3. BEST SELLERS (NO N+1)
         * =========================================================
         */
        $bestSellers = OrderItem::selectRaw('
                order_items.product_id,
                SUM(order_items.quantity) as total_sold
            ')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->groupBy('order_items.product_id')
            ->orderByDesc('total_sold')
            ->with('product:id,name,image,price')
            ->limit(5)
            ->get();

        /**
         * =========================================================
         * 4. LOW STOCK INGREDIENT
         * =========================================================
         */
        $lowStock = Ingredient::where('stock', '<', 10)
            ->orderBy('stock')
            ->limit(20)
            ->get();

        /**
         * =========================================================
         * 5. RETURN VIEW
         * =========================================================
         */
        return view('livewire.dashboard', [
            'lowStock'      => $lowStock,

            'todaySales'    => $stats->today_sales ?? 0,
            'todayCount'    => $stats->today_count ?? 0,
            'totalSales'    => $stats->total_sales ?? 0,
            'monthlySales'  => $stats->monthly_sales ?? 0,
            'totalOrders'   => $stats->total_orders ?? 0,

            'bestSellers'   => $bestSellers,

            'chartLabels'   => $chartLabels,
            'chartRevenue'  => $chartRevenue,
            'chartOrders'   => $chartOrders,
        ]);
    }
}
