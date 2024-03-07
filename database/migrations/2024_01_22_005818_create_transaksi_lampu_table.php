<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiLampuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_lampu', function (Blueprint $table) {
            $table->id();
            $table->string('id_Transaksi_lampu');
            $table->string('id_lampu');
            $table->string('Watt_lampu');
            $table->string('Kode_hardware');     
            $table->integer('Biaya_lampu');
            $table->time('Start_waktu');
            $table->time('End_waktu');
            $table->dateTime('Date')->nullable();
            $table->string('id_ruangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaksi_lampu');
    }
}
