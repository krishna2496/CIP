<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserTableChangeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `user` CHANGE `first_name` `first_name` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
            
            \DB::statement("ALTER TABLE `user` CHANGE `last_name` `last_name` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;");
           
            \DB::statement("ALTER TABLE `user` CHANGE `employee_id` `employee_id` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'none'");
			
			\DB::statement("ALTER TABLE `user` CHANGE `department` `department` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'none';");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `user` CHANGE `first_name` `first_name` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL");
            
            \DB::statement("ALTER TABLE `user` CHANGE `last_name` `last_name` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;");
           
            \DB::statement("ALTER TABLE `user` CHANGE `employee_id` `employee_id` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'none'");
			
			\DB::statement("ALTER TABLE `user` CHANGE `department` `department` VARCHAR(60) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'none';");
        });
    }
}
