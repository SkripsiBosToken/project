<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'products';
    protected $fillable = [
        'name',
        'category_id'
    ];

    public function category() : HasOne {
        return $this->hasOne(Category::class, 'category_id', 'id');
    }

    public function product_variants() : HasMany {
        return $this->hasMany(Product_Variant::class, 'id', 'product_id');
    }
}
