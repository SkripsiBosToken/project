<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product_Variant extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'product_variants';
    protected $fillable = [
        'name_type',
        'photo',
        'description',
        'price',
        'stock',
        'visiblity',
        'product_id'
    ];

    public function product() : HasOne {
        return $this->hasOne(Product::class, 'product_id', 'id');
    }

    public function cart_items() : HasMany {
        return $this->hasMany(Cart_Item::class, 'id', 'product_variant_id');
    }

    public function order_items() : HasMany {
        return $this->hasMany(Order_Item::class, 'id', 'product_variant_id');
    }
}
