<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserCustomFieldTableChangeDataTypeOfTranslationsColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_custom_field', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `user_custom_field` CHANGE `translations` `translations` JSON");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_custom_field', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `user_custom_field` CHANGE `translations` `translations` TEXT");
        });
    }
}
