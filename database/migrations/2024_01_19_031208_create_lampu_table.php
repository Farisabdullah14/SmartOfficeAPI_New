<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLampuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lampu', function (Blueprint $table) {

            $table->id();
            $table->string('id_lampu')->unique();
            $table->string('jenis_lampu');
            $table->integer('watt_lampu');     
            $table->string('Kode_hardware');     
            $table->string('id_ruangan');
            // $table->foreign('id_ruangan')->references('id_ruangan')->on('ruangan');
            $table->enum('Status', ['Aktif', 'Nonaktif', 'Rusak','Maintenance'])->nullable();

            $table->timestamps();


            $table->index('id_lampu');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lampu');
    }
}
