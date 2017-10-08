<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminPanelNavbar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_panel_navbar', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias', 40)->unique();
            $table->string('name', 40)->unique();
            $table->integer('admin_navbar_part_id')->unsigned();
            $table->foreign('admin_navbar_part_id')->references('id')->on('admin_navbar_parts');
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
        Schema::dropIfExists('admin_panel_navbar');
    }
}
