<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->bigIncrements('user_id')->unsigned();
            $table->string('first_name', 16);
            $table->string('last_name', 16);
            $table->string('email', 128)->unique();
            $table->string('password', 255);
            $table->string('avatar', 128);
            $table->integer('timezone_id')->unsigned();
            $table->integer('language_id')->unsigned(); //FK
            $table->integer('availability_id')->unsigned();
            $table->text('why_i_volunteer');
            $table->string('employee_id', 16);
            $table->string('department', 16);
            $table->string('manager_name', 16);
            $table->integer('city_id')->unsigned(); // FK cities id
            $table->integer('country_id')->unsigned();//FK countries id
            $table->text('profile_text');
            $table->string('linked_in_url', 255);
            $table->enum('status', ['0', '1'])->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('timezone_id')->references('timezone_id')->on('timezone')->onDelete('CASCADE')->onUpdate('CASCADE');
            // cross database
            $table->foreign('language_id')->references('language_id')->on('language')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('availability_id')->references('availability_id')->on('user_availability')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('city_id')->references('city_id')->on('city')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('country_id')->references('country_id')->on('country')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user');
    }
}
