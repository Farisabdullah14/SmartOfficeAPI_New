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
            $table->string('id_lampu'); // Sesuaikan tipe data dengan tabel lampu
            $table->foreign('id_lampu')->references('id_lampu')->on('lampu');
            $table->string('Watt_lampu');
            $table->string('Kode_hardware');     
            $table->decimal('Biaya_lampu', 8, 2); // Ubah tipe data menjadi decimal
            $table->dateTime('Start_waktu');
            $table->dateTime('End_waktu');
            $table->String('id_tarif_listrik');

            $table->decimal('tarif_per_kwh');
            // $table->dateTime('Date')->nullable();
            $table->string('id_ruangan');
            $table->foreign('id_ruangan')->references('id_ruangan')->on('ruangan');

            $table->unsignedBigInteger('id_pengguna'); // Menambahkan kolom id_pengguna
            // $table->foreign('id_pengguna')->references('id')->on('pcd_master_users');
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
        Schema::table('transaksi_lampu', function (Blueprint $table) {
            $table->dropForeign(['id_lampu']); // Menambahkan perintah untuk menjatuhkan foreign key constraint
            $table->dropForeign(['id_pengguna']); // Menambahkan perintah untuk menjatuhkan foreign key constraint
            $table->dropForeign(['id_ruangan']); // Menambahkan perintah untuk menjatuhkan foreign key constraint

        });

        Schema::dropIfExists('transaksi_lampu');
    }
}
