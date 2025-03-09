<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\Product_Variant;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */
    
     public function get()
    {
        $datas = Product::with('product_variants', 'category')->get();
        return $datas;
    }

    public function getById($id)
    {
        $data = Product::with('product_variants', 'category')->find($id);
        return $data;
    }

    public function create(Request $request)
    {
        $data = new Product();
        $data->id = Str::uuid()->toString();
        $data->name = $request['name'];
        $data->category_id = $request['category_id'];
        $data->save();
    }

    public function update(Request $request, $id)
    {
        $data = Product::find($id);
        $data->name = $request['name'];
        $data->category_id = $request['category_id'];
        $data->save();
    }

    public function delete($id)
    {
        Product_Variant::where('product_id', $id)->delete();
        Product::find($id)->delete();
    }
}