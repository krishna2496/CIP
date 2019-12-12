<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableCountryTranslation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('country_translation', function (Blueprint $table) {
            $table->bigIncrements('country_translation_id');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('language_id');
            $table->string('name', 255);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('country_id')->references('country_id')->on('country')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('country_translation');
    }
}
