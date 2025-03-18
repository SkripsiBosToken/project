<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('tables-data', [AdminController::class, 'users']);
    Route::prefix('admin')->group(function () {

        Route::prefix('user')->group(function () {

            Route::get('users', [AdminController::class, 'users'])->name('data.pelanggan');
            Route::get('user/{id}', [AdminController::class, 'users_detail'])->name('detail.pelanggan');

        });
    });
});
