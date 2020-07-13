<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDonationAttribute extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('donation_attribute', function (Blueprint $table) {
            $table->uuid('donation_attribute_id')->primary();
            $table->unsignedBigInteger('mission_id');
            $table->string('goal_amount_currency', 3);
            $table->unsignedBigInteger('goal_amount')->nullable();
            $table->enum('show_goal_amount', ['1', '0'])->default('0');
            $table->enum('show_donation_percentage', ['1', '0'])->default('0');
            $table->enum('show_donation_meter', ['1', '0'])->default('0');
            $table->enum('show_donation_count', ['1', '0'])->default('0');
            $table->enum('show_donors_count', ['1', '0'])->default('0');
            $table->enum('disable_when_funded', ['1', '0'])->default('0');
            $table->enum('is_disabled', ['1', '0'])->default('0');
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('mission_id')->references('mission_id')->on('mission')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('donation_attribute');
    }
}
