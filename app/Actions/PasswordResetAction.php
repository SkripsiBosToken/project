<?php

namespace App\Actions;

use App\Models\Password_Reset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PasswordResetAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */

     public function get(){
        $datas = Password_Reset::all();
        return $datas;
     }

     public function getByEmail($email){
        $data = Password_Reset::where('email', $email)->first();
        return $data;
     }

     public function create($request){
        $id = Str::uuid()->toString();
        $data = new Password_Reset();
        $data->email = $request['email'];
        $data->token = $request['token'];
        $data->expired = time() + 1800;
        $data->save();
     }

     public function getByToken($token){
        $data = Password_Reset::where('token', $token)->first();
        return $data;
     }

     public function deleteByEmail($email){
        Password_Reset::where('email', $email)->delete();
     }
}
