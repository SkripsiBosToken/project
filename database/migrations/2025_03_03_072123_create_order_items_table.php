<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('price');
            $table->integer('quantity');
            $table->integer('subtotal');
            $table->foreignUuid('order_id')->references('id')->on('orders');
            $table->foreignUuid('cart_id')->nullable()->references('id')->on('carts');
            $table->foreignUuid('product_variant_id')->nullable()->references('id')->on('product_variants');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
