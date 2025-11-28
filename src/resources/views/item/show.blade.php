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
                    @php
                        $profileId = auth()->check() ? auth()->user()->profile->id : null;
                        $liked = $profileId && $item->likes->where('profile_id', $profileId)->count() > 0;
                    @endphp
                    @auth
                        <img src="{{ asset('/icon_heart.png') }}" alt="いいね" class="like" data-item-id="{{ $item->id }}" style="{{ $liked ? 'filter: invert(32%) sepia(95%) saturate(7497%) hue-rotate(346deg) brightness(95%) contrast(109%); cursor:pointer;' : 'cursor:pointer;' }}">
                        <span class="like-count">{{ $item->likes->count() }}</span>
                    @else
                        <img src="{{ asset('/icon_heart.png') }}" alt="いいね" title="ログインするとお気に入り登録できます" style="cursor:pointer;" onclick="window.location='{{ route('login') }}'">
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
                <form action="{{ route('purchase.show', ['item' => $item->id]) }}" method="get">
                    <button type="submit" class="button">購入手続きへ</button>
                </form>
            @endif
        @else
            <form action="{{ route('login', ['redirect' => url()->current()]) }}" method="get">
            <button type="submit" class="button">購入手続きへ</button>
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
                    <div class="comment-header">
                        @if($comment->profile->profile_image_url)
                            <img src="{{ asset('storage/' . $comment->profile->profile_image_url) }}" alt="プロフィール画像">
                        @else
                            <div class="placeholder-circle"></div>
                        @endif
                        <strong>{{ $comment->profile->name }}</strong>
                    </div>
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
    document.querySelectorAll('.like').forEach(likeBtn => {
        const likeCount = likeBtn.nextElementSibling;

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

                if(data.liked){
                    likeBtn.style.filter = "invert(32%) sepia(95%) saturate(7497%) hue-rotate(346deg) brightness(95%) contrast(109%)";
                } else {
                    likeBtn.style.filter = "none";
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
@endsection