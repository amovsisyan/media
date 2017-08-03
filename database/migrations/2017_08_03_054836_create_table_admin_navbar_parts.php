<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminNavbarParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_navbar_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias', 40);
            $table->string('name', 40);
            $table->integer('admin_navbar_id')->unsigned();
            $table->foreign('admin_navbar_id')->references('id')->on('admin_navbar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_navbar_parts');
    }
}
