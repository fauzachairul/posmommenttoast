<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Ingredient;
use App\Models\Supply;
use App\Models\SupplyItem;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockInManager extends Component
{
    use WithPagination;

    /* ===============================
     | FORM INPUT
     =============================== */
    public $transactionDate;
    public $supplierName = 'Pasar';
    public $notes;

    /* ===============================
     | CART
     =============================== */
    public $cart = []; // ingredient_id => [id, name, unit, qty, cost]

    /* ===============================
     | TEMP INPUT
     =============================== */
    public $selectedIngredientId;
    public $inputQty = 1;
    public $inputCost = 0;

    /* ===============================
     | MOUNT
     =============================== */
    public function mount()
    {
        $this->transactionDate = now()->format('Y-m-d');
    }

    /* ===============================
     | VALIDATION RULES
     =============================== */
    protected function rules()
    {
        return [
            'supplierName'         => 'required|string|max:150',
            'transactionDate'      => 'required|date',
            'cart'                 => 'required|array|min:1',
            'cart.*.qty'           => 'required|numeric|min:1',
            'cart.*.cost'          => 'required|numeric|min:0',
        ];
    }

    /* ===============================
     | ADD ITEM
     =============================== */
    public function addItem()
    {
        $this->validate([
            'selectedIngredientId' => 'required|exists:ingredients,id',
            'inputQty'             => 'required|numeric|min:1',
            'inputCost'            => 'required|numeric|min:0',
        ]);

        $ingredient = Ingredient::select('id', 'name', 'unit')
            ->findOrFail($this->selectedIngredientId);

        if (isset($this->cart[$ingredient->id])) {
            $this->cart[$ingredient->id]['qty']  += $this->inputQty;
            $this->cart[$ingredient->id]['cost'] = $this->inputCost;
        } else {
            $this->cart[$ingredient->id] = [
                'id'   => $ingredient->id,
                'name' => $ingredient->name,
                'unit' => $ingredient->unit,
                'qty'  => $this->inputQty,
                'cost' => $this->inputCost,
            ];
        }

        $this->reset(['selectedIngredientId', 'inputQty', 'inputCost']);
    }

    public function removeItem($id)
    {
        unset($this->cart[$id]);
    }

    /* ===============================
     | SAVE SUPPLY (STOCK IN)
     =============================== */
    public function saveSupply()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            /**
             * ---------------------------------------------
             * 1. GENERATE CODE (LOCKED)
             * ---------------------------------------------
             */
            $code = $this->generateCode();

            /**
             * ---------------------------------------------
             * 2. HITUNG TOTAL
             * ---------------------------------------------
             */
            $totalCost = collect($this->cart)
                ->sum(fn($item) => $item['qty'] * $item['cost']);

            /**
             * ---------------------------------------------
             * 3. CREATE SUPPLY HEADER
             * ---------------------------------------------
             */
            $supply = Supply::create([
                'transaction_code' => $code,
                'supplier_name'    => $this->supplierName,
                'transaction_date' => $this->transactionDate,
                'total_cost'       => $totalCost,
                'notes'            => $this->notes,
            ]);

            /**
             * ---------------------------------------------
             * 4. BULK INSERT ITEMS
             * ---------------------------------------------
             */
            $items = [];
            $stockIncrement = [];

            foreach ($this->cart as $item) {
                $items[] = [
                    'supply_id'    => $supply->id,
                    'ingredient_id' => $item['id'],
                    'quantity'     => $item['qty'],
                    'unit_cost'    => $item['cost'],
                    'subtotal'     => $item['qty'] * $item['cost'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                $stockIncrement[$item['id']] =
                    ($stockIncrement[$item['id']] ?? 0) + $item['qty'];
            }

            SupplyItem::insert($items);

            /**
             * ---------------------------------------------
             * 5. UPDATE STOK (1 INGREDIENT = 1 QUERY)
             * ---------------------------------------------
             */
            foreach ($stockIncrement as $ingredientId => $qty) {
                Ingredient::where('id', $ingredientId)
                    ->lockForUpdate()
                    ->increment('stock', $qty);
            }

            DB::commit();

            /**
             * ---------------------------------------------
             * 6. RESET STATE
             * ---------------------------------------------
             */
            $this->reset(['cart', 'notes']);
            $this->supplierName   = 'Pasar';
            $this->transactionDate = now()->format('Y-m-d');

            session()->flash(
                'message',
                "Stok masuk berhasil. Kode: {$code}"
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            session()->flash(
                'error',
                'Gagal menyimpan stok masuk.'
            );
        }
    }

    /* ===============================
     | GENERATE CODE (SAFE)
     =============================== */
    private function generateCode()
    {
        $date = now()->format('Ymd');

        $last = Supply::whereDate('created_at', today())
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $sequence = $last
            ? intval(substr($last->transaction_code, -3)) + 1
            : 1;

        return sprintf('IN-%s-%03d', $date, $sequence);
    }

    /* ===============================
     | RENDER
     =============================== */
    public function render()
    {
        return view('livewire.stock-in-manager', [
            'ingredients' => Ingredient::select('id', 'name', 'unit')
                ->orderBy('name')
                ->get(),

            'supplies' => Supply::with([
                'items.ingredient:id,name,unit'
            ])
                ->latest()
                ->paginate(5),
        ]);
    }
}
