<?php

namespace App\Actions;

use App\Models\Rate;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RateAction
{
   /**
    * @param \Illuminate\Http\Request
    * @return false|string $token
    */
   public function get()
   {
      $datas = Rate::with('user.role')->get();
      return $datas;
   }

   public function getByOrder($order_id)
   {
      $datas = Rate::with('user.role')->where('order_id', $order_id)->get();
      return $datas;
   }

   public function getWithNum($number)
   {
      $datas = Rate::with('user.role')->take($number)->get();
      return $datas;
   }

   public function create($request){
      $id = Str::uuid()->toString();
      $data = new Rate();
      $data->id = $id;
      $data->rate = $request['rate'];
      $data->message = $request['message'];
      $data->order_id = $request['order_id'];
      $data->user_id = $request['user_id'];
      $data->save();
      
      return $id;
   }
}
