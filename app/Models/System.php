<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'system';
    protected $fillable = [
        'name',
        'logo',
        'visi',
        'misi',
        // Nama kolomnya `special_product`; typo lama membuat atribut ini
        // tidak pernah bisa di-mass-assign.
        'special_product',
        'our_customer',
        'our_service',
        'our_coverage',
        'social_media',
        'office_address',
        'promo_event',
        'phone_number'
    ];
}
