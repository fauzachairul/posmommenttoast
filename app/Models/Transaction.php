<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'details' => 'array',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];
}
