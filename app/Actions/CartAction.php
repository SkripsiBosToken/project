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

    public function create($request){
        $data = new Cart();
        $data->user_id = $request['user_id'];
        $data->save();
        return $data;
    }

    /**
     * Menjamin setiap user selalu punya keranjang. Sebelumnya pemanggil
     * langsung mengakses getByUser(...)[0] sehingga error bila user belum
     * memiliki baris cart (mis. user hasil seeder atau registrasi lama).
     */
    public function firstOrCreateForUser($user_id)
    {
        $cart = Cart::with('cart_items.product_variant.product')
            ->where('user_id', $user_id)
            ->first();

        if (! $cart) {
            $cart = new Cart();
            $cart->user_id = $user_id;
            $cart->save();
            $cart->load('cart_items.product_variant.product');
        }

        return $cart;
    }

}
