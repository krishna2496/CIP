<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMissionRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_ratings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('mission_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('ratings')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            // Relation defined between mission_ratings(mission_id) with missions(id)
            $table->foreign('mission_id')->references('id')->on('missions')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between mission_ratings(user_id) with users(id)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mission_ratings');
    }
}
