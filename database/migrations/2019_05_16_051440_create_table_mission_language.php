<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMissionLanguage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mission_language', function (Blueprint $table) {
            
            $table->bigIncrements('mission_language_id')->unsigned();
            $table->integer('mission_id')->unsigned();
<<<<<<< HEAD
            $table->integer('language_id')->length(11)->default(1);
=======
            $table->integer('language_id')->length(1)->default(1);
>>>>>>> bc96dc5c7f42094658daa98b438f49cffaa08ef1
            $table->string('title');
            $table->text('description'); 
            $table->text('objective'); 
            $table->timestamps();
<<<<<<< HEAD
            $table->foreign('mission_id')->references('mission_id')->on('missions')->onDelete('CASCADE')->onUpdate('CASCADE');
=======
            $table->softDeletes();
            $table->foreign('mission_id')->references('mission_id')->on('mission')->onDelete('CASCADE')->onUpdate('CASCADE');
>>>>>>> bc96dc5c7f42094658daa98b438f49cffaa08ef1
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
<<<<<<< HEAD
        Schema::dropIfExists('table_mission_language');
=======
        Schema::dropIfExists('mission_language');
>>>>>>> bc96dc5c7f42094658daa98b438f49cffaa08ef1
    }
}
