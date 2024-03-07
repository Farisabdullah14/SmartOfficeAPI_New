<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTriggerOnCreateIsiTransaksiLampu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
       
        CREATE TRIGGER before_insert_isi_otomatis_transaksi_lampu
        BEFORE INSERT ON transaksi_lampu
        FOR EACH ROW
        BEGIN
            -- Set default values for Biaya_lampu, Start_waktu, End_waktu, and Date
            SET NEW.Biaya_lampu = IFNULL(NEW.Biaya_lampu, 0);
            SET NEW.Start_waktu = IFNULL(NEW.Start_waktu, CURRENT_TIME());
            SET NEW.End_waktu = IFNULL(NEW.End_waktu, "00:00:00");
            SET NEW.Date = IFNULL(NEW.Date, CURRENT_TIMESTAMP());
        END');    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS before_insert_isi_otomatis_transaksi_lampu');
    }
}
