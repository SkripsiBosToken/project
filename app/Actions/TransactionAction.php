<?php

namespace App\Actions;

use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

     public function getById($id){
        $data = Transaction::find($id);
        return $data;
     }

     public function getByOrderId($order_id){
        $data = Transaction::where('order_id', $order_id)->first();
        return $data;
     }

     public function updateStatus($id, $status){
        $data = Transaction::find($id);
        $data->status = $status;
        $data->save();
     }

     public function create($request){
        $data = new Transaction();
        $data->id = Str::uuid()->toString();
        $data->status = $request['status'];
        $data->transaction_id = $request['transaction_id'];
        $data->invoice_id = $request['invoice_id'];
        $data->order_id = $request['order_id'];
        $data->save();
     }
}