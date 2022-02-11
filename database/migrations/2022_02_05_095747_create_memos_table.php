<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()    /* マイグレーションを実行したときに実行される */
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->unsignedBigInteger('id',true);  // BigInteger桁数の大きい数値型のカラム unsigned符号なし（外部キー制約を使うため．符号ありだと使えない）
            $table->longText('content');    // memoの内容．
            $table->unsignedBigInteger('user_id');  // usersテーブルに登録されてるのじゃないと登録できん（外部キー制約）
            $table->softDeletes();  // 論理削除を定義→deleted_at（削除された時間）を自動的に生成．データベースから完全削除するのではないが，deleted_atに値を入れることで形式的に削除すること．
            // timestampと書いてしまうと，レコード挿入時に更新時に値が入らないため，DB::rawで直接記述．
            $table->timestamp('updated_at')->default(\DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->timestamp('created_at')->default(\DB::raw('CURRENT_TIMESTAMP'));
            $table->foreign('user_id')->references('id')->on('users'); // 外部キー制約
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()/* ロールバック（1前の状態に戻るとき）*/
    {
        Schema::dropIfExists('memos');/*drop テーブルの削除*/
    }
}
