<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Actions\RoleAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::find(Auth::user()->id);
        $role_action = new RoleAction();
        $admin_role = $role_action->getByName('Admin');
        if ($user['role_id'] === $admin_role['id']) {
            return $next($request);
        } else {
            return redirect()->route('home');
        }
    }
}
