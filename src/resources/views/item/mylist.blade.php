@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/items.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection

@section('content')
<div class="item-content">
    <div class="toppage-list">
        <a class="recommend" href="#">おすすめ</a>
        <a class="mylist" href="{{route('item.mylist')}}">マイリスト</a>
    </div>
    <div class="product-row">
    @forelse($mylists as $mylist)
        @php $item = $mylist->item; @endphp
        @if($item)
            <div class="item-card">
                <a href="{{ route('item.show', ['item_id' => $item->id]) }}">
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                @if($item->is_sold)
                    <div class="sold-label">Sold</div>
                @endif
                <p>{{ $item->name }}</p>
            </div>
        @endif
    @empty
        <p>マイリストに商品はありません。</p>
    @endforelse
</div>
</div>
@endsection