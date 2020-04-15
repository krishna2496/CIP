<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMissionMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('mission_id')->unsigned();
            $table->string('media_name',255);
            $table->string('media_type',5);            
            $table->timestamps();
            $table->softDeletes();

            // Relation defined between mission_media(mission_id) with mission(id)
            $table->foreign('mission_id')->references('id')->on('missions')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mission_media');
    }
}
