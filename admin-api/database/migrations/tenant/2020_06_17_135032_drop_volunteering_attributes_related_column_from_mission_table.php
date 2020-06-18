<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropColumnAvailabilityIdFromMissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mission', function (Blueprint $table) {
            $table->dropForeign('mission_availability_id_foreign');
            $table->dropIndex('mission_availability_id_foreign');
            $table->dropColumn('availability_id');
            $table->dropColumn('total_seats');
            $table->dropColumn('is_virtual');
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
            $table->foreign('availability_id')->references('availability_id')->on('availability')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->integer('total_seats')->nullable();
            $table->enum('is_virtual',['0','1'])->default('0');
        });
    }
}
