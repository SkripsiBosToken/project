<?php

namespace App\Http\Controllers;

use App\Actions\CartAction;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //

    public function cart(CartAction $cart_action){
        $cart = $cart_action->get()[0]; //debug
        return view('customer.cart', compact('cart'));
    }
}
