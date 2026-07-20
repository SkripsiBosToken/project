<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product_Variant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'product_variants';
    protected $fillable = [
        'name_type',
        'photo',
        'description',
        'price',
        'stock',
        // Nama kolom di database adalah "visibility"; typo lama membuat
        // atribut ini tidak pernah bisa di-mass-assign.
        'visibility',
        'product_id'
    ];

    public function product() : HasOne {
        return $this->hasOne(Product::class, 'id', 'product_id')->withTrashed();
    }

    public function cart_items() : HasMany {
        return $this->hasMany(Cart_Item::class, 'product_variant_id', 'id');
    }

    public function order_items() : HasMany {
        return $this->hasMany(Order_Item::class, 'product_variant_id', 'id');
    }
}
