<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LampuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menyiapkan data dummy untuk diisi ke dalam tabel lampu
        $lampu = [
            [
                'id_lampu' => 'LMP_001',
                'jenis_lampu' => 'LED',
                'watt_lampu' => 9,
                'Kode_hardware' => 'HDR_001',
                'id_ruangan' => 'RGN_001',
            ],
            [
                'id_lampu' => 'LMP_002',
                'jenis_lampu' => 'Neon',
                'watt_lampu' => 18,
                'Kode_hardware' => 'HDR_001',
                'id_ruangan' => 'RGN_002',
            ],
            // Tambahkan data lainnya sesuai kebutuhan
        ];

        // Memasukkan data dummy ke dalam tabel lampu
        DB::table('lampu')->insert($lampu);
    }
}
