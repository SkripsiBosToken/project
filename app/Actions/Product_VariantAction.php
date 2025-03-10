<?php

namespace App\Actions;

use App\Models\Product_Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Product_VariantAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

     public function get(){
        $datas = Product_Variant::get();
        return $datas;
     }

     public function getById($id){
        $datas = Product_Variant::with('product.category')->find($id);
        return $datas;
     }

     public function updateStock($id, $stock){
        $data = Product_Variant::find($id);
        $data->stock = $stock;
        $data->save();
     }

    //  public function getById(){
    //     $data
    //  }
    
}