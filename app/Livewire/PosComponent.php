<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PosComponent extends Component
{
    /* ===============================
     |  STATE TRANSAKSI
     =============================== */
    public $cart = [];
    public $customerName;
    public $transactionDate;
    public $totalAmount = 0;

    /* ===============================
     |  MOUNT
     =============================== */
    public function mount()
    {
        $this->transactionDate = now()->format('Y-m-d H:i');
    }

    /* ===============================
     |  VALIDATION RULES
     =============================== */
    protected function rules()
    {
        return [
            'customerName' => 'required|string|max:255',
            'cart'         => 'required|array|min:1',
        ];
    }

    /* ===============================
     |  GENERATE KODE TRANSAKSI
     |  ⚠️ WAJIB DIPANGGIL DALAM TRANSAKSI
     =============================== */
    private function generateTransactionCode()
    {
        $today = now()->format('Ymd');

        $lastOrder = Order::whereDate('created_at', today())
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $sequence = $lastOrder
            ? intval(substr($lastOrder->transaction_code, -3)) + 1
            : 1;

        return sprintf('TRX-%s-%03d', $today, $sequence);
    }

    /* ===============================
     |  CART
     =============================== */
    public function addToCart($productId)
    {
        $product = Product::select('id', 'name', 'price', 'image')
            ->find($productId);

        if (!$product) return;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'qty'   => 1,
            ];
        }

        $this->recalculateTotal();
    }

    public function updateQty($productId, $action)
    {
        if (!isset($this->cart[$productId])) return;

        if ($action === 'plus') {
            $this->cart[$productId]['qty']++;
        } else {
            if ($this->cart[$productId]['qty'] > 1) {
                $this->cart[$productId]['qty']--;
            } else {
                unset($this->cart[$productId]);
            }
        }

        $this->recalculateTotal();
    }

    private function recalculateTotal()
    {
        $this->totalAmount = collect($this->cart)
            ->sum(fn($item) => $item['price'] * $item['qty']);
    }


    /* ===============================
     |  CHECKOUT
     =============================== */
    public function checkout()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $trxCode = $this->generateTransactionCode();

            /**
             * 1. CREATE ORDER
             */
            $order = Order::create([
                'transaction_code' => $trxCode,
                'customer_name'    => $this->customerName,
                'transaction_date' => now(),
                'total_amount'     => $this->totalAmount,
                'status'           => 'pending',
            ]);

            /**
             * 2. BULK INSERT ORDER ITEMS
             */
            $items = [];

            foreach ($this->cart as $item) {
                $items[] = [
                    'order_id'          => $order->id,
                    'product_id'        => $item['id'],
                    'quantity'          => $item['qty'],
                    'price_at_purchase' => $item['price'],
                    'subtotal'          => $item['price'] * $item['qty'],
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }

            OrderItem::insert($items);

            DB::commit();

            /**
             * 3. RESET STATE
             */
            $this->reset([
                'cart',
                'customerName',
                'totalAmount',
            ]);

            session()->flash(
                'message',
                "Pesanan {$trxCode} berhasil masuk ke Transaksi Aktif."
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            session()->flash(
                'error',
                'Gagal memproses transaksi.'
            );
        }
    }

    /* ===============================
     |  RENDER
     =============================== */
    public function render()
    {
        return view('livewire.pos-component', [
            'products' => Product::select('id', 'name', 'price', 'image')->get(),
        ]);
    }
}
