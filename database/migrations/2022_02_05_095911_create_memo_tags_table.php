<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemoTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()    // memoとtagをつなぐ中間テーブル．
    {
        Schema::create('memo_tags', function (Blueprint $table) {
            $table->unsignedBigInteger('memo_id');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('memo_id')->references('id')->on('memos');
            $table->foreign('tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('memo_tags');
    }
}
