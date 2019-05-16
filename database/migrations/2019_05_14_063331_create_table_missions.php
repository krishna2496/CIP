<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMissions extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('missions', function (Blueprint $table) {
            $table->bigIncrements('mission_id')->unsigned();
            $table->bigInteger('organisation_id')->unsigned();
            $table->bigInteger('theme_id')->unsigned();
            $table->bigInteger('city_id')->unsigned();
            $table->bigInteger('country_id')->unsigned();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('total_seats');
            $table->dateTime('application_deadline');
            $table->enum('publication_status', ['0', '1'])->default(0);
            $table->text('objective');
            $table->text('description');
            $table->string('organisation_name',255);
            $table->timestamps();
            $table->softDeletes();
            // Relation defined between missions(city_id) with cities(id)
            $table->foreign('city_id')->references('city_id')->on('cities')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between missions(country_id) with counties(id)
            $table->foreign('country_id')->references('country_id')->on('counties')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('theme_id')->references('mission_theme_id')->on('mission_themes')->onDelete('CASCADE')->onUpdate('CASCADE');
//            $table->foreign('organisation_id')->references('organisation_id')->on('cities')->onDelete('CASCADE')->onUpdate('CASCADE');
//            $table->foreign('theme_id')->references('theme_id')->on('counties')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('missions');
    }

}
