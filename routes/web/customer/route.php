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

Route::get('register', [GuestController::class, 'sign_up'])->name('register');
Route::post('register', [GuestController::class, 'register'])->name('register');

Route::get('forgot-password', [GuestController::class, 'forgotPassword'])->name('forgot-password');
Route::post('forgot-password', [GuestController::class, 'sendResetPassword'])->name('forgot-password');

Route::get('reset-password/{token}', [GuestController::class, 'resetPassword'])->name('reset-password');
Route::post('reset-password/{token}', [GuestController::class, 'requestResetPassword'])->name('reset-password');

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
    Route::get('cancel-payment/{id}', [CustomerController::class, 'cancelPayment'])->name('cancel.payment');

    Route::get('order-list', [CustomerController::class, 'order_list'])->name('order-list');
    Route::get('order-detail/{id}', [CustomerController::class, 'order_detail'])->name('order-detail');

    Route::get('profile', [CustomerController::class, 'profile'])->name('profile');
    Route::post('profile', [CustomerController::class, 'updateProfile'])->name('profile.update');

    Route::post('/submit-review', [CustomerController::class, 'submitReview']);

    Route::get('logout', [CustomerController::class, 'logout'])->name('logout');
});
