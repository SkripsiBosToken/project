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
        $datas = Order::get();
        return $datas;
    }


    public function  getById($id)
    {
        $data = Order::find($id);
        return $data;
    }

    public function getByStatus($status)
    {
        $datas = Order::where('status', $status)->get();
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
}
