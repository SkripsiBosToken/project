<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'orders';
    protected $fillable = [
        'status',
        'total_price',
        'shipping_address',
        'shipping_address',
        'user_id'
    ];

    public function order_items() : HasMany {
        return $this->hasMany(Order_Item::class, 'order_id', 'id');
    }

    public function transaction() : HasOne {
        return $this->hasOne(Transaction::class, 'order_id', 'id');
    }

    public function user() : HasOne {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function rate() : HasOne {
        return $this->hasOne(Rate::class, 'id', 'order_id');
    }
}
