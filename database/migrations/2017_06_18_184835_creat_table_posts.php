<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use \App\Http\Controllers\Data\DBColumnLengthData;

class CreatTablePosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alias', DBColumnLengthData::POSTS_TABLE['alias'])->unique();
            $table->string('header', DBColumnLengthData::POSTS_TABLE['header']);
            $table->string('text', DBColumnLengthData::POSTS_TABLE['text']);
            $table->string('image', DBColumnLengthData::POSTS_TABLE['image']);
            $table->integer('subcateg_id')->unsigned();
            $table->foreign('subcateg_id')->references('id')->on('subcategories')->onDelete('cascade');
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
        Schema::dropIfExists('posts');
    }
}
