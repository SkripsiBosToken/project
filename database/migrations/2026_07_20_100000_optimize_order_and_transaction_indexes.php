<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Callback Midtrans mencari pesanan lewat `orders.transaction_id`, tapi
     * kolom itu bertipe TEXT dan tanpa index sehingga setiap notifikasi
     * memicu full table scan dan tidak bisa diindeks.
     *
     * Migration ini mengubahnya menjadi VARCHAR agar bisa diindeks, dan
     * menambahkan index untuk query yang paling sering dipakai.
     */
    public function up(): void
    {
        // SQLite (dipakai test suite) tidak butuh — dan tidak mendukung —
        // MODIFY COLUMN; tipe kolomnya sudah fleksibel dan bisa diindeks.
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `orders` MODIFY `transaction_id` VARCHAR(191) NULL');
            DB::statement('ALTER TABLE `transactions` MODIFY `invoice_id` VARCHAR(191) NULL');
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->index('transaction_id', 'orders_transaction_id_index');
            // Dipakai halaman "Pesanan Saya" dan filter status di admin.
            $table->index(['user_id', 'status'], 'orders_user_id_status_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('transaction_id', 'transactions_transaction_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_transaction_id_index');
            $table->dropIndex('orders_user_id_status_index');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_transaction_id_index');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE `orders` MODIFY `transaction_id` TEXT NOT NULL');
            DB::statement('ALTER TABLE `transactions` MODIFY `invoice_id` VARCHAR(255) NOT NULL');
        }
    }
};
