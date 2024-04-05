<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TriggerIdOtomatisTabelRuangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER generate_id_ruangan BEFORE INSERT ON ruangan
            FOR EACH ROW
            BEGIN
                DECLARE next_id INT;
                
                SELECT MAX(CAST(SUBSTRING(id_ruangan, 5) AS SIGNED)) + 1
                INTO next_id
                FROM ruangan;
                
                SET next_id = IFNULL(next_id, 1);
                
                SET NEW.id_ruangan = CONCAT("RGN_", LPAD(next_id, 3, "0"));

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
        DB::unprepared('DROP TRIGGER IF EXISTS generate_id_ruangan');
    }
}
