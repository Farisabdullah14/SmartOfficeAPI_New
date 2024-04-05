<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ACSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Menyiapkan data dummy untuk diisi ke dalam tabel ac
        $ac = [
            [
                'id_AC' => 'AC_001',
                'jenis_ac' => 'Split',
                'watt_ac' => 1000,
                'kode_hardware' => 'HDR_001',
                'id_ruangan' => 'RGN_001',
                'daya_va' => '1200',
                'paard_kracht' => '1',
                'status' => 'Aktif' // Perbaikan: tambahkan key 'status'
            ],
            [
                'id_AC' => 'AC_002',
                'jenis_ac' => 'Window',
                'watt_ac' => 1500,
                'kode_hardware' => 'HDR_001',
                'id_ruangan' => 'RGN_002',
                'daya_va' => '1800 ',
                'paard_kracht' => '1 ',
                'status' => 'Nonaktif' // Perbaikan: tambahkan key 'status'

            ],
            // Tambahkan data lainnya sesuai kebutuhan
        ];

        // Memasukkan data dummy ke dalam tabel ac
        DB::table('ac')->insert($ac);
    }
}