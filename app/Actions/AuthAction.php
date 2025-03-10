<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthAction
{
    /**
     * @param \Illuminate\Http\Request
     * @return false|string $token
     */


    public function loginUser(Request $request)
    {
        $credential = [
            'username' => $request['username'],
            'password' => $request['password'],
        ];

        if (Auth::attempt($credential)) {
            return Auth::user();
        }
        return false;
    }

    public function getuser()
    {
        return User::with('role')->find(Auth::id());
    }

    public function logout()
    {
        Auth::logout();
    }
}
