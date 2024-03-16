<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiAcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksi_ac', function (Blueprint $table) {
            $table->id();
            $table->string('id_Transaksi_AC');
            $table->string('id_ruangan');
            $table->string('id_AC');
            $table->string('Watt_AC');
            $table->integer('Kecepatan_kipas');
            $table->string('Kode_hardware');
            $table->integer('Kecepatan_Pendingin')->nullable();
            $table->string('Mode')->nullable();
            $table->integer('Temp')->nullable();
            $table->time('Time')->nullable();
            $table->string('Swing')->nullable();
            $table->integer('Biaya_AC')->nullable();
            $table->time('Start_waktu')->nullable();
            $table->time('End_waktu')->nullable();
            $table->date('Date')->nullable();
            $table->string('Status')->nullable();
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
        Schema::dropIfExists('transaksi_ac');
    }
}
