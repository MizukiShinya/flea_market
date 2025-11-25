@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/user.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
<div class="user-content">
    <h1 class="title">ログイン</h1>
    <form class="user-form" action="{{route('login.post')}}" method="post">
        @csrf
        <label for="email">メールアドレス</label>
        <input type="email" name="email" value="{{old('email')}}">
        @error('email')
            <p class="error-message">{{$message}}</p>
        @enderror
        <label for="password">パスワード</label>
        <input type="password" name="password">
        @error('password')
            <p class="error-message">{{$message}}</p>
        @enderror
        <button class="button" type="submit">ログインする</button>
        <a class="user-link" href="{{route('register')}}">会員登録はこちら</a>
    </form>
</div>
@endsection
