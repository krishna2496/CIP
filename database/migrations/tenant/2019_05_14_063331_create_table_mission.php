<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMission extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mission', function (Blueprint $table) {
            $table->bigIncrements('mission_id')->unsigned();
            $table->bigInteger('theme_id')->unsigned();
            $table->bigInteger('city_id')->unsigned();
            $table->bigInteger('country_id')->unsigned();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('total_seats');
            $table->dateTime('application_deadline');
            $table->enum('publication_status', ['0', '1'])->default(0);
            $table->bigInteger('organisation_id')->unsigned();
            $table->string('organisation_name',255);
            $table->timestamps();
            $table->softDeletes();
            // Relation defined between missions(city_id) with cities(id)
            $table->foreign('city_id')->references('city_id')->on('city')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between missions(country_id) with counties(id)
            $table->foreign('country_id')->references('country_id')->on('county')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('theme_id')->references('mission_theme_id')->on('mission_theme')->onDelete('CASCADE')->onUpdate('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('mission');
    }

}
