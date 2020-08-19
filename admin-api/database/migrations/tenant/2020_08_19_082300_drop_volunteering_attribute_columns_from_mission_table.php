<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropVolunteeringAttributeColumnsFromMissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mission', function (Blueprint $table) {
            \DB::statement("ALTER TABLE `mission` DROP `availability_id`, `total_seats`, `is_virtual` ");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mission', function (Blueprint $table) {
            $table->unsignedBigInteger('availability_id');
            $table->integer('total_seats')->nullable();
            $table->enum('is_virtual', ['0', '1'])->default('0');
            $table->foreign('availability_id')->references('availability_id')->on('availability')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }
}
