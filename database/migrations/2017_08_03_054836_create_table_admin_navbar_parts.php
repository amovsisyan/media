<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdminNavbarParts extends Migration
{
    private $table = 'admin_navbar_parts';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias', 40)->unique();
            $table->string('name', 40)->unique();
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
        Schema::dropIfExists($this->table);
    }
}
