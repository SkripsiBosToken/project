<?php

namespace App\Actions;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

    public function get()
    {
        $data = Cart::with('user', 'cart_items.product_variant.product', 'order_items')->get();
        return $data;
    }

    public function getById($id){
        $data = Cart::with('user', 'cart_items', 'order_items')->find($id);
        return $data;
    }

    public function getByUser($user_id)
    {
        $data = Cart::with('user', 'cart_items.product_variant.product', 'order_items')->where('user_id', $user_id)->get();
        return $data;
    }

}
