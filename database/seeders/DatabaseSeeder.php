<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use RuanganSeeder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
    //  $this->call(RuanganSeeder::class);
    $this->call([
        LampuSeeder::class,
        RuanganSeeder::class,
    ]);

}
}
