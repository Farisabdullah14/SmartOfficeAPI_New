<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTriggerCalculateBiayaAndSetEndWaktu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER calculate_biaya_and_set_end_waktu
        BEFORE UPDATE ON transaksi_lampu
        FOR EACH ROW
        BEGIN
            -- Set End_waktu to the current time
            SET NEW.End_waktu = CURRENT_TIME();
            
            -- Calculate Biaya_lampu based on the formula: Biaya_lampu = Watt_lampu * (End_waktu - Start_waktu)
            SET NEW.Biaya_lampu = NEW.Watt_lampu * TIME_TO_SEC(TIMEDIFF(NEW.End_waktu, NEW.Start_waktu)) / 3600;
        END
    ');       }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `calculate_biaya_and_set_end_waktu`');
    }
}
