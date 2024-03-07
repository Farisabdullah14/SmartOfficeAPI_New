<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTriggerIdOtomatisTabelAc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER generate_id_ac BEFORE INSERT ON ac
        FOR EACH ROW
        BEGIN
            DECLARE next_id INT;
            
            SELECT MAX(CAST(SUBSTRING(id_AC, 4) AS SIGNED)) + 1
            INTO next_id
            FROM ac;
            
            SET next_id = IFNULL(next_id, 1);
            
            SET NEW.id_AC = CONCAT("AC_", LPAD(next_id, 3, "0"));
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
        DB::unprepared('DROP TRIGGER IF EXISTS generate_id_ac');
    }
}
