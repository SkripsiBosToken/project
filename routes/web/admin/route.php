<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('tables-data', [AdminController::class, 'users']);
    Route::prefix('admin')->group(function () {

        Route::prefix('user')->group(function () {
            Route::get('users', [AdminController::class, 'users'])->name('data.pelanggan');
            Route::get('user/rate', [AdminController::class, 'rates'])->name('data.rate');
            Route::get('user/{id}', [AdminController::class, 'users_detail'])->name('detail.pelanggan');
        });

        Route::prefix('order')->group(function () {
            Route::get('orders', [AdminController::class, 'orders'])->name('data.pesanan');
            Route::get('histories', [AdminController::class, 'historyOrder'])->name('data.riwayat.pesanan');
            Route::get('history/report', [AdminController::class, 'reportHistory'])->name('data.riwayat.laporan.pesanan');
            Route::get('order/{id}', [AdminController::class, 'order_detail'])->name('detail.pesanan');
            Route::get('order/update-status/{id}/{status}', [AdminController::class, 'update_status'])->name('ubah-status.pesanan');
            Route::get('order/receipt/{id}', [AdminController::class, 'generateInvoice'])->name('nota.pesanan');
        });

        Route::prefix('catalogue')->group(function () {
           Route::get('catalogues', [AdminController::class, 'catalogues'])->name('data.katalog');
           Route::get('catalogue/add', [AdminController::class, 'addCatalogue'])->name('data.katalog.tambah');
           Route::put('catalogue/store', [AdminController::class, 'storeCatalogue'])->name('data.katalog.store');
           Route::get('catalogue/{id}', [AdminController::class, 'detailCatalogue'])->name('data.katalog.detail');
           Route::put('catalogue/update/{id}', [AdminController::class, 'updateCatalogue'])->name('data.katalog.update');
           Route::get('catalogue/delete/{id}', [AdminController::class, 'deleteCatalogue'])->name('data.katalog.hapus');
        });

        Route::prefix('category')->group(function (){
           Route::get('category', [AdminController::class, 'categories'])->name('data.kategori');
           Route::get('category/add', [AdminController::class, 'addCategory'])->name('data.kategori.tambah');
           Route::post('category/store', [AdminController::class, 'storeCategory'])->name('data.kategori.store');
           Route::get('category/{id}', [AdminController::class, 'detailCategory'])->name('data.kategori.detail');
           Route::post('category/update/{id}', [AdminController::class, 'updateCategory'])->name('data.kategori.update');
           Route::get('category/delete/{id}', [AdminController::class, 'deleteCategory'])->name('data.kategori.hapus');
        });

        Route::prefix('system')->group(function () {
            Route::get('setting', [AdminController::class, 'setting'])->name('setting');
            Route::put('setting/update', [AdminController::class, 'updateSetting'])->name('system.update');

            Route::get('setting/special-product', [AdminController::class, 'specialProduct'])->name('setting.special-product');
            Route::put('setting/special-product/update', [AdminController::class, 'updateSpecialSetting'])->name('system.special-product.update');
            
            Route::get('setting/customer', [AdminController::class, 'ourCustomer'])->name('setting.customer');
            Route::put('setting/customer/update', [AdminController::class, 'updateOurCustomer'])->name('system.customer.update');

            Route::get('setting/service', [AdminController::class, 'service'])->name('setting.service');
            Route::put('setting/service/update', [AdminController::class, 'updateService'])->name('system.service.update');

            Route::get('setting/social-media', [AdminController::class, 'socialMedia'])->name('setting.social-media');
            Route::put('setting/social-media/update', [AdminController::class, 'updateSocialMedia'])->name('system.social-media.update');
            
            Route::get('setting/event', [AdminController::class, 'event'])->name('setting.event');
            Route::put('setting/event/update', [AdminController::class, 'updateEvent'])->name('system.event.update');
        });
    });
});
