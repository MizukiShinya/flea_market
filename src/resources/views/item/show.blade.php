@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/item-detail.css') }}">
@endsection

@section('content')
<div class="item-content">
    <div class="product__image-area">
        <img src="{{ asset('storage/' . $item->item_image_url) }}" alt="商品画像">
    </div>
    <div class="product__description-area">
        <div class="product-title">
            <h1 class="product-name">{{ $item->item_name }}</h1>
            <p class="brand">{{ $item->brand }}</p>
            <p class="price">
                <span style="font-size: 30px;">¥</span>
                <span style="font-size: 45px;">{{ number_format($item->price) }}</span><span style="font-size: 30px;">（税込）</span></p>
            <div class="product-actions">
                <div class="product-actions__item">
                    @auth
                        @php $profile = auth()->user()->profile; @endphp
                        <img src="{{ asset('/icon_heart.png') }}" alt="いいね" class="like" data-item-id="{{ $item->id }}">
                        <span class="like-count">{{ $item->likes->count() }}</span>
                    @else
                        <img src="{{ asset('/icon_heart.png') }}" alt="いいね" title="ログインするとお気に入り登録できます">
                        <span class="like-count">{{ $item->likes->count() }}</span>
                    @endauth
                </div>
                <div class="product-actions__item">
                    <img src="{{ asset('/comment.png') }}" alt="コメント">
                    <span class="comment-info">{{ $item->comments->count() }}</span>
                </div>
            </div>
        </div>
        @auth
            @if($item->seller_id === auth()->id())
                <button class="button" disabled>自分の商品です</button>
            @elseif($item->is_sold)
                <button class="button" disabled>売り切れ</button>
            @else
                <form action="{{ route('purchase.show', $item->id) }}" method="get">
                    <button type="submit" class="button">購入手続きへ</button>
                </form>
            @endif
        @else
            <a href="{{ route('login', ['redirect' => url()->current()]) }}" class="button">ログインして購入</a>
        @endauth
        <div class="product__description">
            <h2 class="product-title">商品説明</h2>
            <p>{{ $item->detail }}</p>
        </div>
        <section class="product__info">
            <h2 class="product-title">商品の情報</h2>
            <p>カテゴリー：
                @forelse($item->categories as $category)
                    <span class="tag">{{ $category->content }}</span>
                @empty
                    <span>未分類</span>
                @endforelse
            </p>
            <p>商品の状態：{{ $item->condition }}</p>
        </section>
        <section class="product__comments">
            <h2>コメント （<span class="comment-info">{{ $item->comments->count() }}</span>）</h2>
            @foreach($item->comments as $comment)
                <div class="comment">
                    <strong>{{ $comment->profile->name }}</strong>
                    <p>{{ $comment->content }}</p>
                </div>
            @endforeach
            @auth
                <form action="{{ route('item.comments.store', $item->id) }}" method="post">
                @csrf
                    <h3>商品へのコメント</h3>
                    <textarea name="content">{{ old('content') }}</textarea>
                    @error('content')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="button">コメントを送信する</button>
                </form>
            @endauth
        </section>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const likeBtn = document.querySelector('.like');
    const likeCount = document.querySelector('.like-count');
    if (!likeBtn || !likeCount) return;

    likeBtn.addEventListener('click', () => {
        const itemId = likeBtn.dataset.itemId;
        fetch(`/item/${itemId}/like`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            likeCount.textContent = data.count;
        })
        .catch(err => console.error(err));
    });
});
</script>
@endsection