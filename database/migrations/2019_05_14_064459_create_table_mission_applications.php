<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMissionApplications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_applications', function (Blueprint $table) {
            
            $table->bigIncrements('mission_application_id')->unsigned();
            $table->bigInteger('mission_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            
            $table->dateTime('applied_at');
            $table->text('motivation');
            $table->integer('availabilities');
            $table->enum('approval_status',['AUTOMATICALLY_APPROVED', 'PENDING','REFUSED']);
            $table->timestamps();
            $table->softDeletes();
            // Relation defined between missions(mission_id) with missions(mission_id)
            $table->foreign('mission_id')->references('mission_id')->on('missions')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between missions(user_id) with users(user_id)
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mission_applications');
    }
}
