<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TarifListrikSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tarif_listrik')->insert([
            [
                'kode_tarif' => 'T1',
                'daya' => '20000 VA',
                'tarif_per_kwh' => 1467.28,
                'golongan' => 'R1',
            ],
        ]);
    }
}
