@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/items.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/user.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
<div class="item-content">

    {{-- 検索結果表示（キーワードがある場合のみ） --}}
    @if(!empty($keyword))
        <p class="search-result">「{{ $keyword }}」の検索結果</p>
    @endif

    {{-- プロフィール画像・ユーザー名 --}}
    <div class="user-info">
            @if($profile->profile_image_url)
                <img src="{{ $profile->profile_image_url ? asset('storage/' . $profile->profile_image_url) : asset('images/placeholder.png') }}" alt="プロフィール画像">
            @else
                <div class="placeholder-circle"></div>
            @endif
            <input class="name" type="text" name="name" value="{{old('name', Auth::user()->name)}}">
        <button class="image-button" type="button" onclick="location.href='{{ route('mypage.profile') }}'">プロフィール編集</button>
    </div>

    {{-- 上部ナビ（おすすめ・マイリスト） --}}
    <div class="toppage-list">
        <a class="sell {{ $page === 'sell' ? 'active' : '' }}" href="/mypage?page=sell">出品した商品</a>
        <a class="buy {{ $page === 'buy' ? 'active' : '' }}" href="/mypage?page=buy">購入した商品</a>
    </div>

    {{-- 商品一覧 --}}
    <div class="product-row">
    @if($items->isEmpty())
            <p>該当する商品は見つかりませんでした。</p>
    @elseif(isset($items))
        @foreach($items as $item)
            <div class="product-card">
                <div class="product-card">
                    <div class="product__image-area">
                        <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="商品画像">
                        </a>
                        @if($item->is_sold)
                            <div class="sold-label">Sold</div>
                        @endif
                    </div>
                    <p class="product-title">{{ $item->item_name }}</p>
                </div>
            </div>
        @endforeach
    @endif
    </div>
</div>
@endsection