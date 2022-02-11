@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header">
            新規メモ作成
        </div>
        <form class="card-body my-card-body" action="{{ route('store') }}" method="POST">
            @csrf <!-- なりすまし防止のcsrfトークンを発行．laravelでformを使う際に必要となる．-->
            <div class="form-group">   <!-- データをサーバに投げる→formを使う． -->
                <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">

                </textarea><!-- nameを設定してサーバが受け取れるようにする．-->
            </div>
            @error('content') <!-- errorがあるname属性を渡す-->
                    <div class="alert alert-danger">
                        メモ内容を入力してください
                    </div>
            @enderror
        @foreach($tags as $t)
            <div class="form-check form-check-inline mb-3">
                <input class="form-check-input" type="checkbox" name="tags[]" id="{{$t['id']}}" value="{{$t['id']}}"><!--tagは複数なので配列形式を指定．-->
                <label class="form-check-label" for="{{$t['id']}}">
                    {{$t['name']}}
                </label><!--labelにfor属性で指定することで，labelをクリックしてもチェックボックスにチェックが入る．-->
            </div>
        @endforeach
            <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="新しいタグを入力" /><!-- 1rem親要素の文字サイズと同じサイズ-->
            <button type="submit" class="btn btn-primary">
                保存
            </button><!-- type=submit→actionの位置へ移動^^>
        </form>
    </div>
@endsection
