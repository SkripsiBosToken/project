<?php

namespace App\Actions;

use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RateAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */
    
     public function get($number){
        $datas = Rate::with('user.role')->take($number)->get();
        return $datas;
     }
}