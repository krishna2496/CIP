<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserSkills extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        Schema::create('user_skills', function (Blueprint $table) {
            $table->bigIncrements('user_skill_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('skill_id')->unsigned();            
            $table->timestamps();
            $table->softDeletes();

            // Relation defined between user_skills(user_id) with users(id)
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between user_skills(skill_id) with skills(id)
            $table->foreign('skill_id')->references('skill_id')->on('skills')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_skills');
    }
}
