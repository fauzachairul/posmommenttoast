<?php

use App\Http\Controllers\AuthController;
use App\Livewire\ActiveOrders;
use App\Livewire\Dashboard;
use App\Livewire\IngredientManager;
use App\Livewire\PosComponent;
use App\Livewire\ProductManager;
use App\Livewire\StockInManager;
use App\Livewire\TransactionHistory;
use App\Models\Ingredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/cache-all', function () {

    // 1. Cache Semua Produk + Resep
    Cache::remember('products_all', now()->addHours(2), function () {
        return Product::with('ingredients')->get();
    });

    // 2. Cache Semua Bahan Baku
    Cache::remember('ingredients_all', now()->addHours(2), function () {
        return Ingredient::all();
    });

    // 3. Cache Best Sellers 7 Hari Terakhir
    Cache::remember('best_sellers_7days', now()->addHours(1), function () {
        return OrderItem::selectRaw('product_id, SUM(quantity) as total_sold')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', now()->subDays(7))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->with('product:id,name,image')
            ->get();
    });

    // 4. Cache Pendapatan Bulanan
    Cache::remember('monthly_sales', now()->addHours(1), function () {
        return Order::where('status', 'completed')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
    });

    // 5. Cache Low Stock Ingredients
    Cache::remember('low_stock', now()->addHours(1), function () {
        return Ingredient::where('stock', '<', 10)->get();
    });

    return "Semua cache berhasil di-refresh!";
});

Route::get('/storage-link', function () {
    try {
        // Jalankan artisan storage:link
        Artisan::call('storage:link');

        return "✅ Storage link berhasil dibuat!";
    } catch (\Exception $e) {
        return "Gagal membuat storage link: " . $e->getMessage();
    }
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/ingredients', IngredientManager::class)->name('ingredients');
    Route::get('/products', ProductManager::class)->name('products');
    Route::get('/pos', PosComponent::class)->name('pos');
    Route::get('/transactions', TransactionHistory::class)->name('transactions');
    Route::get('/stock-in', StockInManager::class)->name('stock-in');
    Route::get('/active-orders', ActiveOrders::class)->name('active.orders');


    Route::get('/print-receipt/{order}', function (App\Models\Order $order) {
        // Load relasi item
        $order->load('items.product');
        return view('receipt', compact('order'));
    });
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login-form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route::get('/', function () {
//     return view('welcome');
// });
