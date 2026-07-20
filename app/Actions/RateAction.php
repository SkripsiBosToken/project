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

   /**
    * getByOrder() selalu mengembalikan Collection (selalu truthy), jadi
    * pengecekan "sudah pernah diulas" harus lewat method ini.
    */
   public function existsForOrder($order_id): bool
   {
      return Rate::where('order_id', $order_id)->exists();
   }

   /**
    * Rata-rata & jumlah ulasan untuk SATU produk.
    *
    * Ulasan tersimpan per pesanan, bukan per produk, jadi nilainya ditarik
    * lewat order_items. Ini penting untuk schema.org: menampilkan rating
    * seluruh situs pada halaman satu produk termasuk pelanggaran pedoman
    * rich result Google.
    *
    * @return array{average: float, count: int}
    */
   public function statsForProduct($product_id): array
   {
      $stats = Rate::query()
         ->join('orders', 'orders.id', '=', 'rates.order_id')
         ->join('order_items', 'order_items.order_id', '=', 'orders.id')
         ->join('product_variants', 'product_variants.id', '=', 'order_items.product_variant_id')
         ->where('product_variants.product_id', $product_id)
         ->selectRaw('AVG(rates.rate) as average, COUNT(DISTINCT rates.id) as total')
         ->first();

      return [
         'average' => (float) ($stats->average ?? 0),
         'count' => (int) ($stats->total ?? 0),
      ];
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
