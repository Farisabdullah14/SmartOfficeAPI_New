<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ruangan; // Tambahkan ini untuk mengimpor model Ruangan

class RuanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menyiapkan data dummy untuk diisi ke dalam tabel ruangan
        $ruangan = [
            [
                'id_ruangan' => 'RGN_001',
                'nama_ruangan' => 'Ruangan 1',
                'status' => 'On',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_ruangan' => 'RGN_002',
                'nama_ruangan' => 'Ruangan 2',
                'status' => 'Off',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambahkan data lainnya sesuai kebutuhan
        ];

        // Memasukkan data dummy ke dalam tabel ruangan
        DB::table('ruangan')->insert($ruangan);
    }
}
