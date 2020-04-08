<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterActivityLogTableChangeDataTypeOfObjectValueColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activity_log', function (Blueprint $table) {
             \DB::statement("ALTER TABLE `activity_log` CHANGE `object_value` `object_value` JSON");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activity_log', function (Blueprint $table) {
             \DB::statement("ALTER TABLE `activity_log` CHANGE `object_value` `object_value` JSON");
        });
    }
}
