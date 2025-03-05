<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('customer.welcome'); })->name('home');
Route::get('about', function () { return view('customer.about'); })->name('about');
Route::get('contact-us', function () { return view('customer.contact'); })->name('contact');
Route::get('catalogue', function () { return view('customer.catalogue'); })->name('catalogue');
Route::get('catalogue-detail/{id}', function () { return view('customer.catalogue-detail'); })->name('catalogue-detail');