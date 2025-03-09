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
            $table->text('visi');
            $table->text('misi');
            $table->text('special_product'); // array [product_id]
            $table->text('our_customer'); // obj [name, logo, href]
            $table->text('our_coverage'); // array [koordinat]
            $table->text('social_media'); // obj [name, logo, href]
            $table->text('office_address');
            $table->text('promo_event')->nullable(); // obj [name, banner, href]
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
