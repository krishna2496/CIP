<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTenant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tenant_name',255)->comment('FQDN mapping');
            $table->string('sponsor_id',64);
            $table->enum('skills_enabled',['true','false'])->default('false');
            $table->enum('themes_enabled',['true','false'])->default('false');
            $table->enum('stories_enabled',['true','false'])->default('false');
            $table->enum('news_enabled',['true','false'])->default('false');            
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
        Schema::dropIfExists('tenants');
    }
}
