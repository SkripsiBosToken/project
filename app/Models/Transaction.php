<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transactions';
    protected $fillable = [
        'status',
        'transaction_id',
        'order_id'
    ];

    // public function order() : HasOne {
    //     return $this->hasOne(Order::class, 'id', 'order_id');
    // }
}
