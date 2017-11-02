<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Http\Controllers\Data\DBColumnLengthData;

class CreateTablePostParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('head', DBColumnLengthData::POST_PARTS_TABLE['head'])->nullable();
            $table->string('body', DBColumnLengthData::POST_PARTS_TABLE['body'])->nullable();
            $table->string('foot', DBColumnLengthData::POST_PARTS_TABLE['foot'])->nullable();
            $table->integer('posts_locale_id')->unsigned();
            $table->foreign('posts_locale_id')->references('id')->on('posts_locale')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_parts');
    }
}
