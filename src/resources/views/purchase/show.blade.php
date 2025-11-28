@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="{{ asset('css/pages/item-purchase.css') }}">
@endsection

@section('content')
<div class="purchase-content">
    <form action="{{ route('purchase.checkout', ['item' => $item->id]) }}" method="post">
        @csrf

        <!-- 左カラム -->
        <div class="purchase-left">
            <!-- 商品情報 -->
            <div class="purchase-item">
                <img src="{{ asset('storage/' . $item->item_image_url) }}" alt="{{ $item->item_name }}">
                <div class="purchase-item__block">
                    <h1>{{ $item->item_name }}</h1>
                    <p class="item-price">
                        <span style="font-size: 27px;">¥</span>
                        <span style="font-size: 32px;">{{ number_format($item->price) }}（税込）</span>
                    </p>
                </div>
            </div>

            <!-- 支払い方法 -->
            <div class="purchase-item">
                <div class="purchase-payment">
                    <label for="payment_method">支払い方法</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="">選択してください</option>
                        <option value="card">コンビニ支払い</option>
                        <option value="konbini">カード支払い</option>
                    </select>
                </div>
            </div>
            <div class="purchase-item">
                <div class="purchase-address">
                    <div class="label-link">
                        <label>配送先</label>
                        <a class="change-address" href="{{ route('purchase.addressEdit', ['item' => $item->id]) }}">変更する</a>
                    </div>
                    <div class="address">
                        @if($purchaseAddress)
                            <div>〒{{ $purchaseAddress->postcode }}</div>
                            <div>{{ $purchaseAddress->address }}</div>
                            @if($purchaseAddress->building)
                                <div>{{ $purchaseAddress->building }}</div>
                            @endif
                            <input type="hidden" name="address_id" value="{{ $purchaseAddress->id }}">
                        @else
                            <div>住所が登録されていません。</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- 右カラム -->
        <div class="purchase-right">
            <div class="confirm-surface">
                <div class="purchase-item">
                    <span>商品代金</span>
                    <span>¥{{ number_format($item->price) }}</span>
                </div>
                <div class="purchase-item">
                    <span>支払い方法</span>
                    <span id="selected-method">未選択</span>
                </div>
            </div>
            <button class="button" type="submit">購入する</button>
        </div>
    </form>
</div>

<script>
document.getElementById('payment_method').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex].text || '未選択';
    document.getElementById('selected-method').textContent = selected;
});
</script>
@endsection
