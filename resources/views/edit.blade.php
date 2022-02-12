@extends('layouts.app')

@section('javascript')
    <script src="/public/js/confirm.js"/>

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between my-card-header">
            Edit memo
            <form id="delete-form" action="{{route('destory')}}" method="POST">
                @csrf
                <input type="hidden" name="memo_id" value="{{$edit_memo[0]['id']}}" />
                <button type="submit" class="btn btn-light">
                    <i class="fas fa-trash mr-3" onclick="deleteHandle(event);" type="submit"></i>
                </button>
            </form>
        </div>
        <form class="card-body my-card-body" action="{{ route('update') }}" method="POST">
            @csrf <!-- なりすまし防止のcsrfトークンを発行．laravelでformを使う際に必要となる．-->
            <input type="hidden" name="memo_id" value="{{$edit_memo[0]['id']}}" /> <!-- controller側にidを利用させるため -->
            <div class="form-group">   <!-- データをサーバに投げる→formを使う． -->
                <textarea class="form-control" name="content" rows="3" placeholder="ここにメモを入力">
                    {{$edit_memo[0]['content']}}
                </textarea><!-- nameを設定してサーバが受け取れるようにする．-->
            </div>
            @error('content') <!-- errorがあるname属性を渡す-->
                <div class="alert alert-danger">
                    メモ内容を入力してください
                </div>
            @enderror
        @foreach($tags as $t)
            <div class="form-check form-check-inline mb-3">
                <!-- 三項演算子→if文を1行でかく 条件 ? trueの処理 : falseの処理-->
                <!-- もし$include_tagsにループで回っているタグのidが含まれれば，checked -->
                <input class="form-check-input" type="checkbox" name="tags[]" id="{{$t['id']}}" value="{{$t['id']}}" {{in_array($t['id'],$include_tags) ? 'checked':''}}><!--tagは複数なので配列形式を指定．属性にcheckedとするとdefaultcheckになる．-->
                <label class="form-check-label" for="{{$t['id']}}">
                    {{$t['name']}}
                </label><!--labelにfor属性で指定することで，labelをクリックしてもチェックボックスにチェックが入る．-->
            </div>
        @endforeach
            <input type="text" class="form-control w-50 mb-3" name="new_tag" placeholder="Enter a new tag" />
            <button type="submit" class="btn btn-primary">
                Update
            </button><!-- type=submit→actionの位置へ移動-->
        </form>
    </div>
@endsection
