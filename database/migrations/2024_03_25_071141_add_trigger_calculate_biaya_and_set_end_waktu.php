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
            
            -- Calculate the duration in seconds from Start_waktu to End_waktu
            SET @duration = TIMESTAMPDIFF(SECOND, NEW.Start_waktu, NEW.End_waktu);
            
            -- If the transaction crosses to the next day, add the seconds of the remaining day to the duration
            IF DATE(NEW.Start_waktu) < DATE(NEW.End_waktu) THEN
                SET @duration = @duration + TIME_TO_SEC(TIMEDIFF(DATE_ADD(DATE(NEW.Start_waktu), INTERVAL 1 DAY), NEW.Start_waktu));
            END IF;
            
            -- Calculate Biaya_lampu based on the formula: Biaya_lampu = Watt_lampu * (duration / 3600)
            SET NEW.Biaya_lampu = NEW.Watt_lampu * @duration / 3600;
        END
        ');       
    }
    
    
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
    