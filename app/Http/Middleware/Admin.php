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
        // Sebelumnya method ini melakukan query ulang ke tabel users dan roles
        // pada setiap request, dan error bila role "Admin" tidak ada di
        // database. Relasi role sudah cukup untuk pengecekan ini.
        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        return redirect()->route('home');
    }
}
