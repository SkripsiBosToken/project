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
        Schema::create('system', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('logo');
            $table->string('visi');
            $table->string('misi');
            $table->string('special_product'); // array [product_id]
            $table->string('our_customer'); // obj [name, logo, href]
            $table->string('our_coverage');
            $table->string('social_media'); // obj [name, logo, href]
            $table->string('office_address');
            $table->string('promo_event')->nullable(); // obj [name, banner, href]
            $table->string('phone_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system');
    }
};
