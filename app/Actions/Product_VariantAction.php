<?php

namespace App\Actions;

use App\Models\Product_Variant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Product_VariantAction
{
   /**
    * @param \Illuminate\Http\Request
    * @return false|string $token
    */

   public function get()
   {
      $datas = Product_Variant::get();
      return $datas;
   }

   public function getById($id)
   {
      $datas = Product_Variant::with('product.category')->find($id);
      return $datas;
   }

   public function updateStock($id, $stock)
   {
      $data = Product_Variant::find($id);
      $data->stock = $stock;
      $data->save();
   }

   public function update($request, $id)
   {
      $variant = Product_Variant::find($id);

      $update_data = [
         'name_type'   => $request['name_type'],
         'description' => $request['description'],
         'price'       => $request['price'],
         'stock'       => $request['stock'],
         'photo'       => $request['photos'] ?? '[]',
      ];

      $variant->update($update_data);
   }

   public function create($request)
   {
      $id = Str::uuid()->toString();
      $data = new Product_Variant();
      $data->id = $id;
      $data->name_type = $request['name_type'];
      $data->photo = $request['photos'];
      $data->description = $request['description'];
      $data->price = $request['price'];
      $data->stock = $request['stock'];
      $data->visibility = TRUE;
      $data->product_id = $request['product_id'];
      $data->save();
   }

   public function delete($id)
   {
      $variant = Product_Variant::findOrFail($id);
      $variant->delete();
   }
}
