<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\Ingredient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductManager extends Component
{
    use WithFileUploads;

    public $name;
    public $price;
    public $image;
    public $product_id;
    public $oldImage;

    // resep dinamis
    public $recipe = [];

    public $showForm = false;

    /* =========================================================
     * VALIDATION RULES
     * ========================================================= */
    protected function rules()
    {
        return [
            'name'                      => 'required|string|max:150',
            'price'                     => 'required|numeric|min:0',
            'image'                     => 'nullable|image|max:2048',
            'recipe'                    => 'required|array|min:1',
            'recipe.*.ingredient_id'    => 'required|exists:ingredients,id',
            'recipe.*.quantity_needed'  => 'required|numeric|min:0',
        ];
    }

    /* =========================================================
     * RENDER
     * ========================================================= */
    public function render()
    {
        return view('livewire.product-manager', [
            // pilih kolom penting saja
            'products' => Product::with([
                'ingredients:id,name'
            ])
                ->select('id', 'name', 'price', 'image',)
                ->orderBy('created_at', 'desc')
                ->get(),

            'allIngredients' => Ingredient::select('id', 'name', 'stock', 'unit')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /* =========================================================
     * TAMBAH / HAPUS BARIS RESEP
     * ========================================================= */
    public function addIngredientRow()
    {
        $this->recipe[] = [
            'ingredient_id'   => '',
            'quantity_needed' => 1,
        ];
    }

    public function removeIngredientRow($index)
    {
        unset($this->recipe[$index]);
        $this->recipe = array_values($this->recipe);
    }

    /* =========================================================
     * SAVE (CREATE / UPDATE)
     * ========================================================= */
    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            $data = [
                'name'  => $this->name,
                'price' => $this->price,
            ];

            /**
             * ---------------------------------------------
             * IMAGE UPLOAD
             * ---------------------------------------------
             */
            if ($this->image) {
                if ($this->oldImage) {
                    Storage::disk('public')->delete($this->oldImage);
                }

                $data['image'] = $this->image->store('products', 'public');
            }

            /**
             * ---------------------------------------------
             * SAVE PRODUCT
             * ---------------------------------------------
             */
            $product = Product::updateOrCreate(
                ['id' => $this->product_id],
                $data
            );

            /**
             * ---------------------------------------------
             * SYNC RECIPE
             * ---------------------------------------------
             */
            $pivotData = [];

            foreach ($this->recipe as $item) {
                $pivotData[$item['ingredient_id']] = [
                    'quantity_needed' => $item['quantity_needed'],
                ];
            }

            $product->ingredients()->sync($pivotData);

            DB::commit();

            $this->resetForm();

            session()->flash(
                'message',
                'Produk berhasil disimpan.'
            );
        } catch (\Throwable $e) {
            DB::rollBack();

            session()->flash(
                'error',
                'Gagal menyimpan produk.'
            );
        }
    }

    /* =========================================================
     * EDIT
     * ========================================================= */
    public function edit($id)
    {
        $product = Product::with('ingredients')->findOrFail($id);

        $this->product_id = $product->id;
        $this->name       = $product->name;
        $this->price      = $product->price;
        $this->oldImage   = $product->image;

        $this->recipe = [];

        foreach ($product->ingredients as $ingredient) {
            $this->recipe[] = [
                'ingredient_id'   => $ingredient->id,
                'quantity_needed' => $ingredient->pivot->quantity_needed,
            ];
        }

        $this->showForm = true;
    }

    /* =========================================================
     * RESET FORM
     * ========================================================= */
    public function resetForm()
    {
        $this->reset([
            'name',
            'price',
            'image',
            'oldImage',
            'product_id',
            'recipe',
            'showForm',
        ]);
    }
}
