<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;

Route::get('/', [GuestController::class, 'landing'])->name('home');
Route::get('about', [GuestController::class, 'about'])->name('about');
Route::get('contact-us', [GuestController::class, 'contact'])->name('contact');
Route::get('catalogue', [GuestController::class, 'catalogue'])->name('catalogue');
Route::get('catalogue-detail/{id}', [GuestController::class, 'catalogue_detail'])->name('catalogue-detail');

Route::get('login', [GuestController::class, 'login'])->name('login');
Route::post('login', [GuestController::class, 'auth'])->name('login');

Route::middleware(['auth'])->group(function () {
    Route::get('cart', [CustomerController::class, 'cart'])->name('cart');
    Route::post('cart/add', [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::get('cart/delete/{id}', [CustomerController::class, 'deleteCart'])->name('cart.delete');

    Route::get('checkout', function () {
        return back();
    })->name('checkout');
    Route::post('checkout', [CustomerController::class, 'checkout'])->name('checkout');

    Route::post('checkout/payment', [CustomerController::class, 'checkout_order'])->name('checkout.payment');
    Route::get('payment/{id}', [CustomerController::class, 'payment'])->name('payment');

    Route::get('logout', [CustomerController::class, 'logout'])->name('logout');
});
