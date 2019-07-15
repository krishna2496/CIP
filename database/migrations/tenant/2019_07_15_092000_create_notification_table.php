<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification', function (Blueprint $table) {
            $table->bigIncrements('notification_id');
            $table->unsignedBigInteger('notification_type_id');
            $table->enum('is_read',['0','1'])->default('0')->comment('0: Unread, 1: Read');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('to_user_id');
            $table->unsignedBigInteger('mission_id');
            $table->unsignedBigInteger('comment_id');
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('story_id');
            $table->timestamps();
            $table->softDeletes();

            // Set references with notification_type table
            $table->foreign('notification_type_id')->references('notification_type_id')->on('notification_type')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Set references with user table
            $table->foreign('user_id')->references('user_id')->on('user')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Set references with user table
            $table->foreign('to_user_id')->references('user_id')->on('user')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Set references with mission table
            $table->foreign('mission_id')->references('mission_id')->on('mission')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Set references with comment table
            $table->foreign('comment_id')->references('comment_id')->on('comment')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Set references with message table
            $table->foreign('message_id')->references('message_id')->on('message')->onDelete('CASCADE')->onUpdate('CASCADE');            
            // Set references with story table
            $table->foreign('story_id')->references('story_id')->on('story')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification');
    }
}
