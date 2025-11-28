@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/user.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
<div class="user-content">
    <h1 class="title">会員登録</h1>
    <form class="user-form" action="{{route('register')}}" method="post">
        @csrf
        <label for="name">ユーザー名</label>
        <input type="name" name="name" value="{{old('name')}}">
        @error('name')
            <p class="error-message">{{$message}}</p>
        @enderror
        <label for="email">メールアドレス</label>
        <input type="text" name="email" value="{{old('email')}}">
        @error('email')
            <p class="error-message">{{$message}}</p>
        @enderror
        <label for="password">パスワード</label>
        <input type="password" name="password">
        @error('password')
            <p class="error-message">{{$message}}</p>
        @enderror
        <label for="password_confirmation">確認用パスワード</label>
        <input type="password" name="password_confirmation">
        @error('password_confirmation')
            <p class="error-message">{{$message}}</p>
        @enderror
        <button class="button" type="submit">登録する</button>
        <a class="user-link" href="{{route('login')}}">ログインはこちら</a>
    </form>
</div>
@endsection
