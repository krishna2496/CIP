<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMissionApplication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_application', function (Blueprint $table) {
            
            $table->bigIncrements('mission_application_id')->unsigned();
            $table->bigInteger('mission_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            
            $table->dateTime('applied_at');
            $table->text('motivation');
            $table->integer('availability_id')->unsigned();;
            $table->enum('approval_status',['AUTOMATICALLY_APPROVED', 'PENDING','REFUSED']);
            $table->timestamps();
            $table->softDeletes();
            // Relation defined between missions(mission_id) with missions(mission_id)
            $table->foreign('mission_id')->references('mission_id')->on('mission')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between missions(user_id) with users(user_id)
            $table->foreign('user_id')->references('user_id')->on('user')->onDelete('CASCADE')->onUpdate('CASCADE');

            $table->foreign('availability_id')->references('availability_id')->on('user_availability')->onDelete('CASCADE')->onUpdate('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mission_application');
    }
}
