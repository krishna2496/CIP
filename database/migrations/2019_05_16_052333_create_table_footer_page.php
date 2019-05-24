<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFooterPage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    
    public function up()
    {
<<<<<<< HEAD:database/migrations/2019_05_14_062809_create_table_skills.php
        Schema::create('skills', function (Blueprint $table) {
            $table->bigIncrements('skill_id')->unsigned();
            $table->string('skill_name',64);
            $table->text('translations');
            $table->integer('parent_skill');
=======
        Schema::create('footer_page', function (Blueprint $table) {
            $table->bigIncrements('page_id')->unsigned();
            $table->enum('status',['1', '0'])->default(1);
>>>>>>> bc96dc5c7f42094658daa98b438f49cffaa08ef1:database/migrations/2019_05_16_052333_create_table_footer_page.php
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
        Schema::dropIfExists('footer_page');
    }
}
