<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserAction
{
   /**
    * @param \Illuminate\Http\Request
    * @return false|string $token
    */

   public function get()
   {
      $datas = User::with('role')->get();
      return $datas;
   }

   public function getById($id)
   {
      $data = User::with('orders.order_items.cart.cart_items.product_variant.product', 'orders.order_items.product_variant.product')->find($id);
      return $data;
   }

   public function getByUsername($username)
   {
      $data = User::where("username", $username)->first();
      return $data;
   }

   public function getByEmail($email)
   {
      $data = User::where("email", $email)->first();
      return $data;
   }

   public function create($request)
   {
      $data = new User();
      $data->username = $request['username'];
      $data->name = $request['name'];
      $data->email = $request['email'];
      $data->password = Hash::make($request['password']);
      $data->address = $request['address'];
      $data->phone_number = $request['phone_number'];
      $data->point = $request['point'];
      $data->role_id = $request['role_id'];
      $data->save();

      return $data['id'];
   }

   public function updatePoint($id, $point)
   {
      $data = User::find($id);
      $data->point = $point;
      $data->save();
   }
}
