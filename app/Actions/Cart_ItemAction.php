<?php

namespace App\Actions;

use App\Models\Cart_Item;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Cart_ItemAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

    public function create(Request $request)
    {
        $data = new Cart_Item();
        $data->id = Str::uuid()->toString();
        $data->product_variant_id = $request['product_variant_id'];
        $data->cart_id = $request['cart_id'];
        $data->qty = $request['qty'];
        $data->save();
    }

    public function update(Request $request, $id)
    {
        $data = Cart_Item::find($id);
        $data->product_variant_id = $request['product_variant_id'];
        $data->qty = $request['qty'];
        $data->save();
    }

    public function updateStock($id, $qty)
    {
        $data = Cart_Item::find($id);
        $data->qty = $qty;
        $data->save();
    }

    public function matchCart($cart_id, $product_variant_id)
    {
        $data = Cart_Item::where('cart_id', $cart_id)->where('product_variant_id', $product_variant_id)->get();
        if (count($data) !== 0) {
            return $data[0];
        }
        return false;
    }

    public function getById($id)
    {
        $data = Cart_Item::find($id);
        return $data;
    }

    public function delete($id)
    {
        Cart_Item::find($id)?->delete();
    }

    /**
     * Menghapus item keranjang hanya bila item itu milik user yang sedang
     * login. Mengembalikan false bila item tidak ada atau milik user lain.
     */
    public function deleteForUser($id, $user_id): bool
    {
        $item = Cart_Item::whereKey($id)
            ->whereHas('cart', fn ($query) => $query->where('user_id', $user_id))
            ->first();

        if (! $item) {
            return false;
        }

        $item->delete();

        return true;
    }

    public function deleteByCartId($cart_id)
    {
        Cart_Item::where('cart_id', $cart_id)->delete();
    }
}
