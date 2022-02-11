<?php

namespace App\Providers;

use App\Models\Memo;
use App\Models\Tag;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 全てのメソッドが呼ばれる前に先に呼ばれるメソッド
        view()->composer('*',function($view){

            // インスタンス化→他のファイルで使えるようにする
            $memo_model=new Memo();
            // 自分のメモ取得はMemoモデルに任せる
            $memos=$memo_model->getMyMemo();

            $tags=Tag::where('user_id','=',\Auth::id())
                ->whereNULL('deleted_at')
                ->orderBy('id','DESC')
                ->get();

            $view->with('memos',$memos)->with('tags',$tags);
        });
    }
}
