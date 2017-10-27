<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Http\Controllers\Data\DBColumnLengthData;

class CreateTableCategoriesLocale extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_locale', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', DBColumnLengthData::CATEGORIES_LOCALE_TABLE['name'])->unique();
            $table->integer('categ_id')->unsigned();
            $table->foreign('categ_id')->references('id')->on('categories')->onDelete('cascade');
            $table->integer('locale_id')->unsigned();
            $table->foreign('locale_id')->references('id')->on('locale')->onDelete('cascade');
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
        Schema::dropIfExists('categories_locale');
    }
}
