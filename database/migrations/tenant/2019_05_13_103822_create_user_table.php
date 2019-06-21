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
            $table->string('first_name',16);
            $table->string('last_name',16);
            $table->string('email',128);
            $table->string('password',255);
            $table->string('avatar',128)->default('none'); 
            $table->unsignedBigInteger('timezone_id');
            $table->unsignedInteger('language_id'); //FK 
            $table->unsignedBigInteger('availability_id');
            $table->text('why_i_volunteer');
            $table->string('employee_id',16)->default('none'); 
            $table->string('department',16)->default('none');
            $table->string('manager_name',16)->default('none'); 
            $table->unsignedBigInteger('city_id'); // FK cities id
            $table->unsignedBigInteger('country_id');//FK countries id
            $table->text('profile_text');
            $table->string('linked_in_url',255)->default('none');
            $table->enum('status', ['0', '1'])->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('timezone_id')->references('timezone_id')->on('timezone')->onDelete('CASCADE')->onUpdate('CASCADE');
            // cross database            
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
