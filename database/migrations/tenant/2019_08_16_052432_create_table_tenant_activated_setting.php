<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTenantActivatedSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('tenant_activated_setting', function (Blueprint $table) {
            $table->bigIncrements('tenant_activated_setting_id')->unsigned();
            $table->unsignedBigInteger('setting_id'); // FK tenant_settings id
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('setting_id')->references('setting_id')->on('tenant_settings')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tenant_activated_setting');
    }
}
