<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFooterPagesLanguage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
        Schema::create('footer_pages_language', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->bigInteger('page_id')->unsigned();
            $table->string('title',255);
            $table->text('description');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('page_id')->references('page_id')->on('footer_pages')->onDelete('CASCADE')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('table_footer_pages_language');
    }
}
