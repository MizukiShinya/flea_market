@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/user.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
<div class="user-content">
    <h1 class="title">プロフィール設定</h1>
    <form class="user-form" action="{{route('mypage.update')}}" method="post" enctype="multipart/form-data">
        @csrf
        @method('put')
        <div class="user-image">
            @if($profile->profile_image_url)
                <img src="{{ $profile->profile_image_url ? asset('storage/' . $profile->profile_image_url) : asset('images/placeholder.png') }}" alt="プロフィール画像">
            @else
                <div class="placeholder-circle"></div>
            @endif
            <button class="image-button" type="button">画像を選択する</button>
            <input type="file" id="profile_image_url" name="profile_image_url" accept="image/*" hidden>
        </div>
        <label for="name">ユーザー名</label>
        <input type="text" name="name" value="{{old('name', Auth::user()->name)}}">
        <label for="postcode">郵便番号</label>
        <input type="text" name="postcode" value="{{ old('postcode', $profile->postcode) }}">
        <label for="address">住所</label>
        <input type="text" name="address" value="{{ old('address', $profile->address) }}">
        <label for="building">建物名</label>
        <input type="text" name="building" value="{{ old('building', $profile->building) }}">
        <button class="button" type="submit">更新する</button>
    </form>
</div>
@section('js')
<script>
document.querySelector('.image-button').addEventListener('click', function () {
    document.getElementById('profile_image_url').click();
});

document.getElementById('profile_image_url').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(event) {
        const img = document.querySelector('user-image img');
        if (img) {
            img.src = event.target.result;
        } else {
            const newImg = document.createElement('img');
            newImg.src = event.target.result;
            document.querySelector('.user-image').prepend(newImg);

            const placeholder = document.querySelector('.placeholder-circle');
            if (placeholder) placeholder.remove();
        }
    };
    reader.readAsDataURL(file);
});
</script>
@endsection
@endsection

