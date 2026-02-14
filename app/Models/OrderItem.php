<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded = [];

    // Relasi: Item milik 1 Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi: Item menyimpan info Produk
    public function product()
    {
        return $this->belongsTo(Product::class); // Tambahkan ->withTrashed() jika Anda menggunakan softDeletes
    }
}
