<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PostOnDeleteCascade extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('post_hashtag', function (Blueprint $table) {
            $table->dropForeign('post_hashtag_post_id_foreign');
            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->onDelete('cascade');
        });

        Schema::table('post_parts', function (Blueprint $table) {
            $table->dropForeign('post_parts_post_id_foreign');
            $table->foreign('post_id')
                ->references('id')->on('posts')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
