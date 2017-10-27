<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePostHashtag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_hashtag_locale', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_locale_id')->unsigned();
            $table->foreign('post_locale_id')->references('id')->on('post_locale')->onDelete('cascade');
            $table->integer('hashtags_locale_id')->unsigned();
            $table->foreign('hashtags_locale_id')->references('id')->on('hashtags_locale')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_hashtag');
    }
}
