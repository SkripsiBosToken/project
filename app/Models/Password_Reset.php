<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Password_Reset extends Model
{
    use HasFactory, HasUuids;
    
    protected $table = 'password_resets';
    protected $fillable = [
        'email',
        'token',
        'expired'
    ];
}
