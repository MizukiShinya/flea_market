@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/pages/user.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
@endsection
@section('content')
<div class="user-content">
    <h1 class="title">住所の変更</h1>
    <form class="user-form" action="{{route('purchase.addressUpdate', ['item' => $item->id])}}" method="post">
        @csrf
        @method('PUT')
        <label for="postcode">郵便番号</label>
        <input type="text" name="postcode" value="{{session('purchase_address.postcode', $address->postcode ?? '')}}">
        <label for="address">住所</label>
        <input type="text" name="address"  value="{{session('purchase_address.address', $address->address ?? '')}}">
        <label for="building">建物名</label>
        <input type="text" name="building" value="{{session('purchase_address.building', $address->building ?? '')}}">
        <button class="button" type="submit">更新する</button>
    </form>
</div>
@endsection