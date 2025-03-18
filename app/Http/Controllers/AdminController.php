<?php

namespace App\Http\Controllers;

use App\Actions\SystemAction;
use App\Actions\UserAction;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(){
        return view('admin.dashboard');
    }

    public function users(UserAction $user_action){
        $datas = $user_action->get();
        return view('admin.user.user', compact('datas'));
    }

    public function users_detail($id, UserAction $user_action){
        $data = $user_action->getById($id);
        $orders = $data['orders'];
        // return $data;
        return view('admin.user.detail', compact('data', 'orders'));
    }
}
