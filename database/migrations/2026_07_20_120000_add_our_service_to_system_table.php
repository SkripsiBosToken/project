<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Daftar "Layanan Kami" di halaman Tentang Kami sebelumnya di-hardcode
     * di dalam blade, sehingga admin tidak bisa mengganti foto maupun
     * menambah layanan baru. Sekarang disimpan sebagai JSON di tabel system,
     * mengikuti pola kolom our_customer / social_media.
     *
     * Struktur: [{ "label": string, "image": string, "size": kecil|sedang|besar }]
     */
    public function up(): void
    {
        Schema::table('system', function (Blueprint $table) {
            $table->text('our_service')->nullable()->after('our_customer');
        });

        // Data awal diisi dengan layanan yang selama ini tampil, agar
        // halaman tidak mendadak kosong setelah migrasi dijalankan.
        $defaults = [
            ['label' => 'Katering Perusahaan', 'image' => '/assets/images/image-6.jpg', 'size' => 'kecil'],
            ['label' => 'Acara Pernikahan', 'image' => '/assets/images/image-7.jpg', 'size' => 'kecil'],
            ['label' => 'Snack Box', 'image' => '/assets/images/image-8.png', 'size' => 'kecil'],
            ['label' => 'Hampers', 'image' => '/assets/images/image-9.jpg', 'size' => 'kecil'],
            ['label' => 'Nasi Tumpeng', 'image' => '/assets/images/image-10.jpg', 'size' => 'sedang'],
        ];

        DB::table('system')->update(['our_service' => json_encode($defaults)]);
    }

    public function down(): void
    {
        Schema::table('system', function (Blueprint $table) {
            $table->dropColumn('our_service');
        });
    }
};
