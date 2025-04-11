<?php

namespace App\Actions;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

    public function get()
    {
        $datas = Category::with('products')->get();
        return $datas;
    }

    public function getById($id)
    {
        $data = Category::with('products')->find($id);
        return $data;
    }

    public function create($request)
    {
        $data = new Category();
        $data->id = Str::uuid()->toString();
        $data->name = $request['name'];
        $data->description = $request['description'];
        $data->save();
    }

    public function update($request, $id)
    {
        $data = Category::find($id);
        $data->name = $request['name'];
        $data->description = $request['description'];
        $data->save();
    }

    public function delete($id)
    {
        Category::find($id)->delete();
        // $datas = Product::where('category_id', $id);
        // if (!$datas) {
        //     Category::find($id)->delete();
        // }
    }
}
