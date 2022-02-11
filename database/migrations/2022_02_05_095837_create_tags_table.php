<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->unsignedBigInteger('id',true);  // BigInteger桁数の大きい数値型のカラム unsigned符号なし（外部キー制約を使うため．符号ありだと使えない）
            $table->string('name');
            $table->unsignedBigInteger('user_id');  // 誰が所有しているか？
            $table->softDeletes();  // 論理削除を定義→deleted_at（削除された時間）を自動的に生成．データベースから完全削除するのではないが，deleted_atに値を入れることで形式的に削除すること．
            // timestampと書いてしまうと，レコード挿入時に更新時に値が入らないため，DB::rawで直接記述．
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
