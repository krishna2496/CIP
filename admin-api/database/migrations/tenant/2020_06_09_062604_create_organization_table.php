<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name',255);
            $table->integer('legal_number');
            $table->integer('phone_number');
            $table->string('address_line_1',255);
            $table->string('address_line_2',255);
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->integer('postal_code');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('city_id')->references('city_id')->on('city')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('state_id')->references('state_id')->on('state')->onDelete('CASCADE')->onUpdate('CASCADE');
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
        Schema::dropIfExists('organization');
    }
}
