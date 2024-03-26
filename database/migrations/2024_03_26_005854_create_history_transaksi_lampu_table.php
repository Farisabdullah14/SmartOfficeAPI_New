<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTransaksiLampuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_transaksi_lampu', function (Blueprint $table) {
            $table->id();
            $table->string('id_history_transaksi_lampu');
            $table->string('id_transaksi_lampu');
            $table->string('id_lampu'); // Sesuaikan tipe data dengan tabel lampu yang digunakan
            $table->integer('watt_lampu');
            $table->string('kode_hardware');
            $table->integer('Biaya_lampu');
            $table->dateTime('start_waktu');
            $table->dateTime('end_waktu');
            $table->string('id_ruangan');
            $table->unsignedBigInteger('id_pengguna');
            $table->string('status');
            
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
        Schema::dropIfExists('history_transaksi_lampu');
    }
}
