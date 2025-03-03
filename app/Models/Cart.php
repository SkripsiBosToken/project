<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'carts';
    protected $fillable = [
        'user_id'
    ];

    public function user() : HasOne {
        return $this->hasOne(User::class, 'user_id', 'id');
    }

    public function cart_items() : HasMany {
        return $this->hasMany(Cart_Item::class, 'id', 'product_variant_id');
    }

    public function order_items() : HasMany {
        return $this->hasMany(Order_Item::class, 'id', 'cart_id');
    }
}
