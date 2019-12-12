<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCityTranslation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('city_translation', function (Blueprint $table) {
            $table->bigIncrements('city_translation_id');
            $table->unsignedBigInteger('city_id');
            $table->unsignedBigInteger('language_id');
            $table->string('name', 255);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('city_id')->references('city_id')->on('city')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('language_id')->references('language_id')->on('language')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('city_translation');
    }
}
