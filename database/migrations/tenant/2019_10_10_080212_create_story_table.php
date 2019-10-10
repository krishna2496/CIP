<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('story', function (Blueprint $table) {
            $table->bigIncrements('story_id')->unsigned();
            $table->unsignedBigInteger('mission_id');
            $table->unsignedBigInteger('user_id');
            $table->string('title',255);
            $table->text('description');
            $table->enum('status',['DRAFT', 'PENDING','PUBLISHED','DECLINED']);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('mission_id')->references('mission_id')->on('mission')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('user_id')->references('user_id')->on('user')->onDelete('CASCADE')->onUpdate('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('story');
    }
}
