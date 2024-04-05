<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTriggerIdAutomatisCreateTransaksiAc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER generate_trans_id
        BEFORE INSERT ON transaksi_ac FOR EACH ROW
        BEGIN
            DECLARE max_id INT;
            DECLARE new_id INT;

            SELECT MAX(SUBSTRING(id_Transaksi_AC, 5)) INTO max_id FROM transaksi_ac;

            IF max_id IS NULL THEN
                SET new_id = 1;
            ELSE
                SET new_id = max_id + 1;
            END IF;

            SET NEW.id_Transaksi_AC = CONCAT("TRS_", LPAD(new_id, 3, "0"));

            SET NEW.Waktu_Penggunaan="00:00:00";
        END;
    ');    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS generate_trans_id');
    }
}
