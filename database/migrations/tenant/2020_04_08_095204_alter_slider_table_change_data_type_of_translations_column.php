<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSliderTableChangeDataTypeOfTranslationsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('slider', function (Blueprint $table) {
             \DB::statement("ALTER TABLE `slider` CHANGE `translations` `translations` JSON");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('slider', function (Blueprint $table) {
             \DB::statement("ALTER TABLE `slider` CHANGE `translations` `translations` JSON");
        });
    }
}
