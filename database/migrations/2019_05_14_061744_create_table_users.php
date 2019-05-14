<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');            
            $table->string('employee_id',255);
            $table->string('manager_name',100);
            $table->string('department_name',100);
            $table->string('first_name',64);
            $table->string('last_name',64);
            $table->string('email',255);
            $table->string('password',255);
            $table->bigInteger('city_id')->unsigned();
            $table->bigInteger('country_id')->unsigned();
            $table->string('avatar',255)->comment('User profile picture');
            $table->text('profile_text');
            $table->string('linked_in_url',2000);
            $table->text('why_i_volunteer');
            $table->enum('availability',['anytime','weekend only','work week only']);
            $table->bigInteger('timezone_id')->unsigned();
            $table->bigInteger('language_id')->unsigned();            
            $table->timestamps();
            $table->softDeletes();

            // Relation defined between users(city_id) with cities(id)
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between users(country_id) with counties(id)
            $table->foreign('country_id')->references('id')->on('counties')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between users(language_id) with languages(id)
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('CASCADE')->onUpdate('CASCADE');
            // Relation defined between users(timezone_id) with timezones(id)
            $table->foreign('timezone_id')->references('id')->on('timezones')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
