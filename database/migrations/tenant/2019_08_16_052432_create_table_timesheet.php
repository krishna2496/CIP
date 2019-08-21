<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTimesheet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('timesheet', function (Blueprint $table) {
            $table->bigIncrements('timesheet_id')->unsigned();
            $table->unsignedBigInteger('user_id'); // FK users id
            $table->year('year');
            $table->integer('month')->length(2);
            $table->enum('status', ['PENDING','APPROVED']);
            $table->timestamps();
            $table->softDeletes();
            
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
        Schema::dropIfExists('timesheet');
    }
}
