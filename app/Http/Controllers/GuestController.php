<?php

namespace App\Http\Controllers;

use App\Actions\CategoryAction;
use App\Actions\ProductAction;
use App\Actions\RateAction;
use App\Actions\SystemAction;
use App\View\Components\Card\product;
use Illuminate\Http\Request;


class GuestController extends Controller
{
    public function setting()
    {
        $system_action = new SystemAction();
        return $system_action->get();
    }

    public function getCategories()
    {
        $category_action = new CategoryAction();
        return $category_action->get();
    }

    // View
    public function catalogue(ProductAction $product_action)
    {
        $products = $product_action->get();
        return view('customer.catalogue', compact('products'));
    }

    // View
    public function catalogue_detail(ProductAction $product_action, $id)
    {
        $product = $product_action->getById($id);
        return view('customer.catalogue-detail', compact('product'));
    }

    // View 
    public function landing(SystemAction $system_action, ProductAction $product_action)
    {
        $products = [];
        $setting = $system_action->get();
        $data = json_decode($setting['special_product'], true);
        foreach ($data as $id) {
            $data = $product_action->getById($id);
            array_push($products, $data);
        }

        $our_customers = json_decode($setting['our_customer'],true);
        return view('customer.welcome', compact('setting', 'products', 'our_customers'));
    }

    //view 
    public function about(SystemAction $system_action, RateAction $rate_action){
       $setting = $system_action->get();
       $rates = $rate_action->get(3);
       return view('customer.about', compact('setting', 'rates'));
    }

    //view
    public function contact(SystemAction $system_action){
        $setting = $system_action->get();
        $social_medias = json_decode($setting['social_media'],true);
        return view('customer.contact', compact('setting', 'social_medias'));
    }
}
