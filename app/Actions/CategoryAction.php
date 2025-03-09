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
        $datas = Category::get();
        return $datas;
    }

    public function getById($id)
    {
        $data = Category::find($id);
        return $data;
    }

    public function create(Request $request)
    {
        $data = new Category();
        $data->id = Str::uuid()->toString();
        $data->name = $request['name'];
        $data->description = $request['description'];
        $data->save();
    }

    public function update(Request $request, $id)
    {
        $data = Category::find($id);
        $data->name = $request['name'];
        $data->description = $request['description'];
        $data->save();
    }

    public function delete($id)
    {
        $datas = Product::where('category_id', $id);
        if (!$datas) {
            Category::find($id)->delete();
        }
    }
}
