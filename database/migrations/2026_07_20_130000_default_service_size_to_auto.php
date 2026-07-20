<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Menyetel ukuran kartu layanan yang sudah ada menjadi `otomatis`.
     *
     * Migrasi sebelumnya mengisi semuanya dengan `kecil`, sehingga tiap kartu
     * berlebar sama dan tata letak mosaiknya hilang (kartu tampil menumpuk
     * selebar layar). `otomatis` mengembalikan pola lebar berselang-seling.
     */
    public function up(): void
    {
        foreach (DB::table('system')->get() as $row) {
            $services = json_decode($row->our_service ?? '[]', true);

            if (! is_array($services) || $services === []) {
                continue;
            }

            foreach ($services as $i => $service) {
                $services[$i]['size'] = 'otomatis';
            }

            DB::table('system')->where('id', $row->id)
                ->update(['our_service' => json_encode($services)]);
        }
    }

    public function down(): void
    {
        // Tidak perlu dikembalikan: `size` hanya memengaruhi tampilan.
    }
};
