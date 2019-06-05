<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTenantHasOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenant_has_option', function (Blueprint $table) {
            $table->bigIncrements('tenant_option_id');
            $table->bigInteger('tenant_id')->unsigned();
            $table->string('option_name',64);
            $table->enum('option_value',['0','1'])->default('1')->comment('0: Inactive, 1: Active');
            $table->timestamps();
            $table->softDeletes();

            // Relation defined between tenant_has_option(tenant_id) with tenant(id)
            $table->foreign('tenant_id')->references('tenant_id')->on('tenant')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_has_option');
    }
}
