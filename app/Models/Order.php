<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    // Relasi: 1 Order punya banyak Item
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
