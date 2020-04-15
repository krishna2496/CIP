<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
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
            $table->integer('employee_id'); //FK
            $table->integer('manager_id'); //FK
            $table->integer('department_id'); // (FK departments id) / department
            $table->string('first_name',64);
            $table->string('last_name',64);
            $table->string('email',255)->unique();
            $table->string('password',64);
            $table->integer('city_id'); // FK cities id
            $table->integer('country_id');//FK countries id
            $table->string('avatar');
            $table->text('profile_text');
            $table->text('linked_in_url');
            $table->text('why_i_volunteer');
            $table->string('availability');
            $table->string('timezone',32);
            $table->string('language',2); //FK
            $table->timestamps();
            $table->softDeletes();
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
