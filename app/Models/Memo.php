<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Memo extends Model
{
    use HasFactory;

    public function getMyMemo() // publicどこからでも呼び出せる
    {
        $query_tag=\Request::query('tag');// バックスラッシュを使うとインポートしなくても使える

        // ベースのメソッドstart

        $query=Memo::query()->select('memos.*')  //  memos.* memosの全てのカラムの情報をとってくる
            ->where('user_id','=',\Auth::id())
            ->whereNULL('deleted_at')   // where→絞り込み whereNULL('deleted_at')でdeleted_at==NULLの意になる．
            ->orderBy('updated_at','DESC'); // ASC昇順，DESC降順

        // ベースのメソッドstop

        // ここでメモデータを取得
        // もしクエリパラメータtagがあればタグで絞り込み．タグがなければ全部とってくる．
        if(!empty($query_tag))
        {
            $query->leftJoin('memo_tags','memo_tags.memo_id','=','memos.id')
                ->where('memo_tags.tag_id','=',$query_tag);

        }

        $memos=$query->get();

        return $memos;

    }
}
