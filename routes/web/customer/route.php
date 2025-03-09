<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GuestController;

Route::get('/', [GuestController::class, 'landing'])->name('home');
Route::get('about', [GuestController::class, 'about'])->name('about');
Route::get('contact-us', [GuestController::class, 'contact'])->name('contact');
Route::get('catalogue', [GuestController::class, 'catalogue'])->name('catalogue');
Route::get('catalogue-detail/{id}', [GuestController::class, 'catalogue_detail'])->name('catalogue-detail');

Route::get('cart', [CustomerController::class, 'cart'])->name('cart');