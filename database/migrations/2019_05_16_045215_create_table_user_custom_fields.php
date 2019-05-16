<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUserCustomFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::create('user_custom_fields', function (Blueprint $table) {
            $table->bigIncrements('field_id')->unsigned();
            $table->text('name');
            $table->enum('type', ['Text', 'Email','Drop-down','radio']);
            $table->text('translations');
            $table->integer('is_mandatory')->length(11)->default(1);
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
        Schema::dropIfExists('table_user_custom_fields');
    }
}
