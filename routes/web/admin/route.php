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
    });
});
