<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cart_Item extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'cart_items';
    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'qty'
    ];

    public function cart() : HasOne {
        return $this->hasOne(Cart::class, 'cart_id', 'id');
    }

    public function product_variant() : HasOne {
        return $this->hasOne(Product_Variant::class, 'product_variant_id', 'id');
    }
}
