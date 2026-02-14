<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class ActiveOrders extends Component
{
    /**
     * =========================================================
     * TAMPILKAN ORDER PENDING (NO N+1)
     * =========================================================
     */
    public function render()
    {
        $orders = Order::with([
            'items.product:id,name',
        ])
            ->where('status', 'pending')
            ->orderBy('created_at', 'asc') // FIFO
            ->get();

        return view('livewire.active-orders', compact('orders'));
    }

    /**
     * =========================================================
     * AKSI: DONE (SELESAI + POTONG STOK + PRINT)
     * =========================================================
     */
    public function markAsDone($orderId)
    {
        DB::beginTransaction();

        try {
            /**
             * -----------------------------------------------------
             * 1. LOCK ORDER (ANTI DOUBLE KASIR)
             * -----------------------------------------------------
             */
            $order = Order::where('id', $orderId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($order->status !== 'pending') {
                throw new \Exception('Order sudah diproses.');
            }

            /**
             * -----------------------------------------------------
             * 2. HITUNG TOTAL POTONGAN INGREDIENT (SQL)
             * -----------------------------------------------------
             */
            $ingredientDeduction = DB::table('product_ingredient')
                ->join(
                    'order_items',
                    'product_ingredient.product_id',
                    '=',
                    'order_items.product_id'
                )
                ->where('order_items.order_id', $order->id)
                ->select(
                    'product_ingredient.ingredient_id',
                    DB::raw('SUM(product_ingredient.quantity_needed * order_items.quantity) AS total')
                )
                ->groupBy('product_ingredient.ingredient_id')
                ->pluck('total', 'ingredient_id');

            /**
             * -----------------------------------------------------
             * 3. POTONG STOK (1 INGREDIENT = 1 QUERY)
             * -----------------------------------------------------
             */
            foreach ($ingredientDeduction as $ingredientId => $qty) {
                DB::table('ingredients')
                    ->where('id', $ingredientId)
                    ->decrement('stock', $qty);
            }

            /**
             * -----------------------------------------------------
             * 4. UPDATE STATUS ORDER
             * -----------------------------------------------------
             */
            $order->update([
                'status' => 'completed',
            ]);

            DB::commit();

            /**
             * -----------------------------------------------------
             * 5. PRINT & FEEDBACK
             * -----------------------------------------------------
             */
            $this->dispatch('print-receipt', orderId: $order->id);

            session()->flash(
                'message',
                "Order #{$order->transaction_code} selesai."
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            session()->flash(
                'error',
                'Gagal memproses order: ' . $e->getMessage()
            );
        }
    }

    /**
     * =========================================================
     * AKSI: CANCEL (TANPA SENTUH STOK)
     * =========================================================
     */
    public function markAsCancelled($orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->status !== 'pending') {
            session()->flash(
                'error',
                "Order #{$order->transaction_code} sudah diproses."
            );
            return;
        }

        $order->update(['status' => 'cancelled']);

        session()->flash(
            'error',
            "Order #{$order->transaction_code} dibatalkan. Stok tidak berubah."
        );
    }
}
