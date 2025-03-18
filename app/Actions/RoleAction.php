<?php

namespace App\Actions;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

     public function get(){
        $datas = Role::get();
        return $datas;
     }

     public function getByName($name){
        $data = Role::where('name', $name)->first();
        return $data;
     }
    
}