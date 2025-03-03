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
        'user_id'
    ];

    public function order_items() : HasMany {
        return $this->hasMany(Order_Item::class, 'id', 'order_id');
    }

    public function transaction() : HasOne {
        return $this->hasOne(Transaction::class, 'id', 'order_id');
    }

    public function user() : HasOne {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

}
