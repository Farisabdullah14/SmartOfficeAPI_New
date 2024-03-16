<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSetDefaultTriggerTransaksiAcOn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::unprepared('
            CREATE TRIGGER create_set_default_trigger_transaksi_ac_on BEFORE INSERT ON transaksi_ac
            FOR EACH ROW
            BEGIN
                SET NEW.Biaya_AC = 0;
                SET NEW.Start_waktu = CURRENT_TIME();
                SET NEW.End_waktu = "00:00:00";
                SET NEW.Date = CURRENT_DATE();
                SET NEW.Status = "On";
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
        Schema::dropIfExists('set_default_trigger_transaksi_ac_on');
    }
}
