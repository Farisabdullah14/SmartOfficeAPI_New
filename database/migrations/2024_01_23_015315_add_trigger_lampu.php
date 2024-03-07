<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddTriggerLampu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
        CREATE TRIGGER `before_insert_lampu` BEFORE INSERT ON `lampu`
        FOR EACH ROW BEGIN
         -- Declare variables to store the calculated values
         DECLARE next_id INT;
       
         -- Get the maximum value of id_lampu
         SELECT MAX(CAST(SUBSTRING(id_lampu, 5) AS SIGNED)) + 1
         INTO next_id
         FROM lampu;
       
         -- If the table is empty, set next_id to 1
         SET next_id = IFNULL(next_id, 1);
       
         -- Set the new id_lampu value for the new record
         SET NEW.id_lampu = CONCAT("LMP_", LPAD(next_id, 3, "0"));
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
        DB::unprepared('DROP TRIGGER IF EXISTS `before_insert_lampu`');
    }
}
