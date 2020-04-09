<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserFilterTableChangeDataTypeOfFiltersColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_filter', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `user_filter` CHANGE `filters` `filters` JSON");
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_filter', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `user_filter` CHANGE `filters` `filters` TEXT");
       });
    }
}
