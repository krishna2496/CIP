<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTimesheetItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('timesheet_items', function (Blueprint $table) {
            $table->bigIncrements('timesheet_item_id')->unsigned();
            $table->unsignedBigInteger('timesheet_id'); // FK timesheets id
            $table->unsignedBigInteger('mission_id')->nullable()->default(null); // FK missions id
            $table->time('time');
            $table->integer('action')->length(11);
            $table->dateTime('date_volunteered');
            $table->dateTime('day_volunteered');
            $table->text('notes');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('timesheet_id')->references('timesheet_id')->on('timesheet')->onDelete('CASCADE')->onUpdate('CASCADE');
            $table->foreign('mission_id')->references('mission_id')->on('mission')->onDelete('CASCADE')->onUpdate('CASCADE');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timesheet_items');
    }
}
