@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/item-create.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
<div class="item-content">
    <h1 class="title">商品の出品</h1>
    <form action="{{ route('item.store') }}" method="post" enctype="multipart/form-data">
    @csrf
        <div class="exhibited__product">
            <label for="image">商品画像</label>
            <div class="exhibited__product-image">
                <div class="placeholder-circle">
                    <button class="image-button" type="button">画像を選択する</button>
                </div>
                <input type="file" name="image" id="image" hidden  accept="image/*">
            </div>
        </div>
        <div class="exhibited__product">
            <h2 class="subtitle">商品の詳細</h2>
            <div class="category">
                <label for="category">カテゴリー</label>
                <div class="category-tags">
                    @foreach($categories as $category)
                    <label class="category-tag">
                        <input type="checkbox" name="category_ids[]" value="{{ $category->id }}"> {{ $category->content }}
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="status">
                <label for="status">商品の状態</label>
                <select name="condition" id="status">
                    <option value="">選択してください</option>
                    <option value="新品">新品</option>
                    <option value="良品">目立った傷や汚れなし</option>
                    <option value="やや傷あり">やや傷や汚れあり</option>
                    <option value="粗悪品">状態が悪い</option>
                </select>
            </div>
        </div>
        <div class="exhibited__product">
            <h2 class="subtitle">商品名と説明</h2>
            <label for="item_name">商品名</label>
            <input type="text" name="item_name">
            <label for="brand">ブランド名</label>
            <input type="text" name="brand">
            <label for="detail">商品の説明</label>
            <textarea name="detail"></textarea>
            <label for="price">販売価格</label>
            <input type="text" name="price" placeholder="¥">
        </div>
        <button class="button" type="submit">出品する</button>
    </form>
</div>
@section('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.image-button').addEventListener('click', function () {
        document.getElementById('image').click();
    });

    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            const imgContainer = document.querySelector('.exhibited__product-image');
            const img = imgContainer.querySelector('img');

            if (img) {
                img.src = event.target.result;
            } else {
                const newImg = document.createElement('img');
                newImg.src = event.target.result;
                imgContainer.prepend(newImg);

                const placeholder = imgContainer.querySelector('.placeholder-circle');
                if (placeholder) placeholder.remove();
            }
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
@endsection
