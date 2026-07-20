<?php

namespace App\Providers;

use App\Models\System;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Pengaturan sistem hanya berisi satu baris dan dibutuhkan hampir di
        // setiap halaman, jadi cukup di-resolve sekali per request.
        $this->app->singleton('system.setting', fn () => System::first());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sebelumnya layout, header, footer, dan banner masing-masing
        // memanggil GuestController::setting() sendiri sehingga tabel `system`
        // di-query sampai 4x per halaman. Sekarang dibagikan satu kali.
        View::composer('*', function ($view) {
            // Jangan timpa $setting yang sudah dikirim controller secara eksplisit.
            if (! array_key_exists('setting', $view->getData())) {
                $view->with('setting', app('system.setting'));
            }
        });
    }
}
