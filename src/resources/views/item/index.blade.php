@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/items.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
<div class="item-content">

    {{-- 検索結果表示（キーワードがある場合のみ） --}}
    @if(!empty($keyword))
        <p class="search-result">「{{ $keyword }}」の検索結果</p>
    @endif

    {{-- 上部ナビ（おすすめ・マイリスト） --}}
    <div class="toppage-list">
        <a class="recommend" href="#">おすすめ</a>
        <a class="mylist" href="{{route('item.mylist')}}">マイリスト</a>
    </div>

    {{-- 商品一覧 --}}
    <div class="product-row">
    @if($items->isEmpty())
            <p>該当する商品は見つかりませんでした。</p>
    @else
        @foreach($items as $item)
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
        @endforeach
    @endif
    </div>
</div>
@endsection