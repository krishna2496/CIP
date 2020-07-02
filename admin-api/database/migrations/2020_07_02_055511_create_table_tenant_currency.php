<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTenantCurrency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('table_tenant_currency', function (Blueprint $table) {
            $table->string('code', 3);
            $table->bigIncrements('tenant_id');
            $table->string('default', 3);
            $table->enum('is_active', ['0','1'])->default('0')->comment('0: Inactive, 1: Active');
            $table->timestamps();
            $table->primary(['code', 'tenant_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_tenant_currency');
    }
}
