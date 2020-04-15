<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('organisation_id')->unsigned();
            $table->string('title',255);
            $table->string('mession_theme',64);
            $table->string('mission_type',64);
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->bigInteger('total_seats')->unsigned();
            $table->bigInteger('available_seats')->unsigned();
            $table->dateTime('application_deadline');
            $table->enum('publication_status',['DRAFT','PENDING_APPROVAL','REFUSED','APPROVED','PUBLISHED_FOR_VOTING','PUBLISHED_FOR_APPLYING','UNPUBLISHED'])->default('PENDING_APPROVAL');
            $table->text('objective');
            $table->text('description');
            $table->bigInteger('city_id')->unsigned();
            $table->bigInteger('country_id')->unsigned();
            $table->timestamps();
            $table->softDeletes();

            // Relation defined between missions(city_id) with cities(id)
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between missions(country_id) with counties(id)
            $table->foreign('country_id')->references('id')->on('counties')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('missions');
    }
}
