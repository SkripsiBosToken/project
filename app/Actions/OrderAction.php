<?php

namespace App\Actions;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

    public function get()
    {
        // 'user' ikut di-eager-load: tabel pesanan di admin menampilkan nama
        // pelanggan tiap baris, yang tanpa ini memicu query N+1.
        $datas = Order::with('user', 'transaction', 'order_items.product_variant.product')->get();
        return $datas;
    }

    public function  getById($id)
    {
        $data = Order::with('transaction', 'order_items.product_variant.product.category', 'order_items.cart.cart_items.product_variant.product.category', 'user')->find($id);
        return $data;
    }

    public function getByStatus($status)
    {
        $datas = Order::with('transaction', 'order_items.product_variant.product', 'order_items.cart.cart_items.product_variant.product', 'user')->where('status', $status)->get();
        return $datas;
    }

    public function getByTransactionId($transaction_id)
    {
        $data = Order::where('transaction_id', $transaction_id)->first();
        return $data;
    }

    public function create($request)
    {
        $id = Str::uuid()->toString();
        $data = new Order();
        $data->id = $id;
        $data->status = $request['status'];
        $data->total_price = $request['total_price'];
        $data->transaction_id = $request['transaction_id'];
        $data->shipping_address = $request['shipping_address'];
        $data->user_id = $request['user_id'];
        $data->save();
        return $id;
    }

    public function update($request, $id)
    {
        $data = Order::find($id);
        $data->status = $request['status'];
        $data->total_price = $request['total_price'];
        $data->transaction_id = $request['transaction_id'];
        $data->shipping_address = $request['shipping_address'];
        $data->user_id = $request['user_id'];
        $data->save();
    }


    public function updateStatus($id, $status)
    {
        $data = Order::find($id);
        $data->status = $status;
        $data->save();
    }

    public function updateTransactionId($id, $transaction_id)
    {
        $data = Order::find($id);
        $data->transaction_id = $transaction_id;
        $data->save();
    }

    /**
     * Mengambil pesanan milik user tertentu. Mengembalikan null bila pesanan
     * tidak ada atau bukan milik user tersebut, sehingga pemanggilnya bisa
     * membalas 404 alih-alih membocorkan data pesanan orang lain.
     */
    public function getByIdForUser($id, $user_id)
    {
        return Order::with('transaction', 'order_items.product_variant.product.category', 'order_items.cart.cart_items.product_variant.product.category', 'user')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->first();
    }

    public function delete($id){
        Order::find($id)->delete();
    }
}
