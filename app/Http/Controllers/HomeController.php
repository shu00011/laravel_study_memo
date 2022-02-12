<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Memo;
use App\Models\Tag;
use App\Models\MemoTag;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $tags=Tag::where('user_id','=',\Auth::id())->whereNull('deleted_at')->orderBy('id','DESC')->get();
        //dd($tags);

        return view('create',compact('tags')); //compactで変数を渡す→bladeで変数が使える
    }

    public function store(Request $request) // POSTでは，インスタンス化してrequestが使えるようにする．
    {
        $posts=$request->all(); // requestの値を全てとることができる
        $request->validate(['content'=>'required']);
        // dd(\Auth::id()); // dump dieの略→メソッドの引数に撮った値を展開して止める→データのデバッグ

        $request->session()->regenerateToken(); // 2重送信防止

        // トランザクション開始
        // メモIDをインサートして取得
        // 新規タグが入力されているかチェック
        // 新規タグが既にtagsテーブルに存在するのかチェック
        // 新規タグが既に存在しなければ，tagsテーブルにインサート→IDを取得
        // memo_tagsにインサートして，メモとタグを紐づける
        // トランザクション終了
        // 3つのインサートのうちどれか失敗したらロールバック．

        DB::transaction(function() use($posts)
        {
            $memo_id=Memo::insertGetId(['content'=>$posts['content'],'user_id'=>\Auth::id()]);  // メモIDを取得
            $tag_exists=Tag::where('user_id','=',\Auth::id())->where('name','=',$posts['new_tag'])->exists();
            if((!empty($posts['new_tag']) || $posts['new_tag']==="0")&& !$tag_exists)   // empty()は"0"も空扱いになるため，0というタグを使うことができない．
            {
                // dd('新規タグがある場合');

                $tag_id=Tag::insertGetId(['user_id' => \Auth::id(), 'name'=>$posts['new_tag']]);    // タグIDを取得
                MemoTag::insert(['memo_id'=>$memo_id,'tag_id'=>$tag_id]);
            }

            // 既存タグが紐づけれらた場合→memo_tagsにインサート
            // 複数タグの紐づけには中間テーブルが必要．
            if(!empty($posts['tags'][0])) { // チェックボックスに選択がある場合
                foreach ($posts['tags'] as $tag) {
                    MemoTag::insert(['memo_id' => $memo_id, 'tag_id' => $tag]);
                }
            }
        });

        return redirect(route('home')); // redirect urlを転送すること．
    }

    public function edit($id)
    {
        // 1行のみなのでfind. $edit_memo=Memo::find($id); // findは主キーの$idと一致するものをとってくる．
        $edit_memo=Memo::select('memos.*','tags.id AS tag_id') // ASで衝突を避けて別名でとってくる．
            ->leftJoin('memo_tags','memo_tags.memo_id','=','memos.id')
            ->leftJoin('tags','memo_tags.tag_id','=','tags.id')    // 3つのテーブルをくっつける
            ->where('memos.user_id','=',\Auth::id()) // 複数のテーブルで使われているので，どのテーブルのuser_idか指定
            ->where('memos.id','=',$id)
            ->whereNULL('memos.deleted_at')
            ->get();    // 複数行なのでget.

        $include_tags=[];   // 配列の宣言
        foreach($edit_memo as $memo)
        {
            array_push($include_tags,$memo['tag_id']);
        }
        $tags=Tag::where('user_id','=',\Auth::id())->whereNull('deleted_at')->orderBy('id','DESC')
            ->get();

        return view('edit',compact('edit_memo','include_tags','tags')); //compactで変数を渡す→bladeで変数が使える
    }

    public function update(Request $request) // POSTでは，インスタンス化してrequestが使えるようにする．
    {
        $posts=$request->all(); // requestの値を全てとることができる
        // dd(\Auth::id()); // dump dieの略→メソッドの引数に撮った値を展開して止める→データのデバッグ
        $request->validate(['content'=>'required']);

        $request->session()->regenerateToken(); // 2重送信防止

        // トランザクションスタート
        DB::transaction(function() use($posts)
        {
            Memo::where('id',$posts['memo_id'])->update(['content'=>$posts['content']]);    // updateするときはwhereでどの行を更新するか指定する．そうじゃないと全部の行を更新することになってしまう．
            // 一旦メモとタグの紐づけを削除(物理削除）（中間テーブルにおいては物理削除を使う．基本は論理削除モデル．）
            MemoTag::where('memo_id','=',$posts['memo_id'])->delete();
            // 再度メモとタグの紐づけ
            foreach($posts['tags'] as $tag)
            {
                MemoTag::insert(['memo_id'=>$posts['memo_id'],'tag_id'=>$tag]);
            }
            // もし，新しいタグの入力があれば，インサートして紐付け
            $tag_exists=Tag::where('user_id','=',\Auth::id())->where('name','=',$posts['new_tag'])->exists();
            if((!empty($posts['new_tag']) || $posts['new_tag']==="0")&& !$tag_exists)   // empty()は"0"も空扱いになるため，0というタグを使うことができない．
            {
                // dd('新規タグがある場合');

                $tag_id=Tag::insertGetId(['user_id' => \Auth::id(), 'name'=>$posts['new_tag']]);    // タグIDを取得
                MemoTag::insert(['memo_id'=>$posts['memo_id'],'tag_id'=>$tag_id]);
            }
        });
        // トランザクション終わり

        return redirect(route('home')); // redirect urlを転送すること．
    }

    public function destory(Request $request) // POSTでは，インスタンス化してrequestが使えるようにする．
    {
        $posts=$request->all(); // requestの値を全てとることができる
        // dd(\Auth::id()); // dump dieの略→メソッドの引数に撮った値を展開して止める→データのデバッグ
        Memo::where('id',$posts['memo_id'])->update(['deleted_at'=>date("Y-m-d H:i:s",time())]);

        return redirect(route('home')); // redirect urlを転送すること．
    }
}
