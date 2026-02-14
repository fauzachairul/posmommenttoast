<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];

    // Relasi ke bahan baku
    public function ingredients()
    {
        return $this->belongsToMany(
            Ingredient::class,
            'product_ingredient',
            'product_id',
            'ingredient_id'
        )->withPivot('quantity_needed');
    }
}
