<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    protected $guarded = [];
    protected $casts = ['transaction_date' => 'date'];

    public function items()
    {
        return $this->hasMany(SupplyItem::class);
    }
}
