<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Ingredient;

class IngredientManager extends Component
{
    public $name;
    public $unit;
    public $stock;
    public $ingredient_id;
    public $isEdit = false;

    /**
     * =========================================================
     * VALIDATION RULES
     * =========================================================
     */
    protected function rules()
    {
        return [
            'name'  => 'required|string|max:100',
            'unit'  => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
        ];
    }

    /**
     * =========================================================
     * RENDER
     * =========================================================
     */
    public function render()
    {
        return view('livewire.ingredient-manager', [
            // limit & order biar ringan
            'ingredients' => Ingredient::orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
        ]);
    }

    /**
     * =========================================================
     * CREATE / UPDATE
     * =========================================================
     */
    public function store()
    {
        $this->validate();

        Ingredient::updateOrCreate(
            ['id' => $this->ingredient_id],
            [
                'name'  => $this->name,
                'unit'  => $this->unit,
                'stock' => $this->stock,
            ]
        );

        session()->flash(
            'message',
            $this->isEdit
                ? 'Ingredient berhasil diperbarui.'
                : 'Ingredient berhasil ditambahkan.'
        );

        $this->resetInput();
    }

    /**
     * =========================================================
     * EDIT
     * =========================================================
     */
    public function edit($id)
    {
        $ingredient = Ingredient::findOrFail($id);

        $this->ingredient_id = $ingredient->id;
        $this->name          = $ingredient->name;
        $this->unit          = $ingredient->unit;
        $this->stock         = $ingredient->stock;
        $this->isEdit        = true;
    }

    /**
     * =========================================================
     * DELETE
     * =========================================================
     */
    public function delete($id)
    {
        Ingredient::findOrFail($id)->delete();

        session()->flash('message', 'Ingredient berhasil dihapus.');
    }

    /**
     * =========================================================
     * RESET FORM
     * =========================================================
     */
    public function resetInput()
    {
        $this->reset([
            'name',
            'unit',
            'stock',
            'ingredient_id',
            'isEdit'
        ]);
    }
}
