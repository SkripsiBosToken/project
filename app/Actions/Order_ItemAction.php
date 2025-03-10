<?php

namespace App\Actions;

use App\Models\Order_Item;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Order_ItemAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

     public function get(){
        $datas = Order_Item::get();
        return $datas;
     }

     public function getById($id){
        $data = Order_Item::find($id);
        return $data;
     }

     public function create($request){
        $id = Str::uuid()->toString();
        $data = new Order_Item();
        $data->id = $id;
        $data->price = $request['price'];
        $data->quantity = $request['quantity'];
        $data->subtotal = $request['subtotal'];
        $data->order_id = $request['order_id'];
        $data->cart_id = $request['cart_id'];
        $data->product_variant_id=$request['product_variant_id'];
        $data->save();
        return $id;
     }

     public function update($request, $id){
        $data = Order_Item::find($id);
        $data->price = $request['price'];
        $data->quantity = $request['quantity'];
        $data->subtotal = $request['subtotal'];
        $data->order_id = $request['order_id'];
        $data->cart_id = $request['cart_id'];
        $data->product_variant_id=$request['product_variant_id'];
        $data->save();
     }
    
}