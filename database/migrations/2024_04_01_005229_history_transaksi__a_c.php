<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HistoryTransaksiAC extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_transaksi_AC', function (Blueprint $table) {
            $table->id();
            $table->string('id_history_transaksi_AC');
            $table->string('id_Transaksi_AC');
            $table->string('id_ruangan');
            $table->string('id_AC');
            $table->integer('Watt_AC');
            $table->enum('Kecepatan_kipas', ['Low', 'Medium', 'High'])->nullable();
            $table->string('Kode_hardware');
            $table->enum('Kecepatan_Pendingin', ['Low', 'Medium', 'High'])->nullable();

            $table->string('Mode')->nullable();
            $table->integer('Temp')->nullable();
            $table->time('Time')->nullable();
            $table->string('Swing')->nullable();
            $table->decimal('Biaya_AC',10,2)->nullable();
            $table->dateTime('Start_waktu')->nullable();
            $table->dateTime('End_waktu')->nullable();
            $table->string('id_tarif_listrik')->nullable();
            $table->decimal('tarif_per_kwh', 8, 2)->nullable(); // Misalnya tarif per kWh
            $table->unsignedBigInteger('id_pengguna'); // Menambahkan kolom id_pengguna
            $table->Time('Waktu_Penggunaan'); // Kolom Waktu_Penggunaan (Tipe Data Time) Gabungkan Time dan Start_waktu menjadi satu kolom Waktu_Penggunaan. Hitung selisih End_waktu dan Start_waktu untuk mendapatkan durasi penggunaan AC dalam format Time.
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
        Schema::dropIfExists('history_transaksi_AC');
    }
}
