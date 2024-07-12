<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTriggerForKunciRuangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER generate_id_key_kunci_ruangan BEFORE INSERT ON kunci_ruangan
            FOR EACH ROW
            BEGIN
                DECLARE next_id INT;

                SELECT IFNULL(MAX(CAST(SUBSTRING(id_key, 5) AS UNSIGNED)), 0) + 1
                INTO next_id
                FROM kunci_ruangan;

                SET NEW.id_key = CONCAT("KEY_", LPAD(next_id, 7, "0"));
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
        DB::unprepared('DROP TRIGGER IF EXISTS generate_id_key_kunci_ruangan');
    }
}
