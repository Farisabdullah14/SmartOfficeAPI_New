<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateTriggerForPinActivityRuangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER generate_pin_activity_ruangan BEFORE INSERT ON pin_activity_ruangan
            FOR EACH ROW
            BEGIN
                DECLARE next_id INT;

                SELECT IFNULL(MAX(CAST(SUBSTRING(id_pin_activity_ruangan, 5) AS UNSIGNED)), 0) + 1
                INTO next_id
                FROM pin_activity_ruangan;

                SET NEW.id_pin_activity_ruangan = CONCAT("ACT_", LPAD(next_id, 7, "0"));
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
        Schema::dropIfExists('trigger_for_pin_activity_ruangan');
    }
}
