<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Order;
use App\Models\Address;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    // 購入確認画面
    public function show($item_id){
        $item = Item::findOrFail($item_id);
        $user = Auth::user()->load('profile');

        // 自分の商品は買えないようにする
        if ($item->seller_id === Auth::id()) {
            return redirect()->route('item.show', $item->id);}

        // すでに売却済みなら弾く
        if ($item->is_sold) {
            return redirect()->route('item.show', $item->id);}
        
        // Address テーブルから取得
        $purchaseAddress = Address::where('profile_id', $user->profile->id)->first();

        // まだ登録されていなければ Profile の情報をもとに作成
        if (!$purchaseAddress) {
            $purchaseAddress = Address::create([
                'profile_id' => $user->profile->id,
                'postcode' => $user->profile->postcode ?? '',
                'address' => $user->profile->address ?? '',
                'building' => $user->profile->building ?? '',
            ]);
        }
        return view('purchase.show', compact('item', 'user', 'purchaseAddress'));
    }

    // 購入実行画面
    public function store(Request $request, Item $item){
        $user = Auth::user();
        if (!$user->profile) {
            return back()->with('error', 'プロフィールが登録されていません。');
        }

        $profileId = $user->profile->id;
        $addressId = $request->input('address_id');
        $paymentMethod = $request->input('payment_method', '未選択');

        // 購入履歴を保存（purchaseテーブル）
        $purchase =Order::create([
            'profile_id' => $user->profile->id,
            'item_id' => $item->id,
            'address_id' => $addressId,
            'payment_method' => $paymentMethod,
        ]);
        // 商品を購入済みに更新
        $item->is_sold = true;
        $item->save();
            return redirect()->route('mypage.index', ['page' => 'buy']);
    }
    // 配送先変更画面
    public function addressEdit($item_id)
    {
        $profileId = Auth::user()->profile->id;
        $address = Address::where('profile_id', $profileId)->first();
        $item = Item::findOrFail($item_id);
        return view('purchase.address', compact('address', 'item'));
    }

    //配送先更新処理
    public function addressUpdate(Request $request, $item_id){
        $validated = $request->validate([
            'postcode' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        $profileId = Auth::user()->profile->id;
        $address = Address::updateOrCreate(
            ['profile_id' => $profileId],$validated
        );
        session(['purchase_address_id' => $address->id]);

        return redirect()->route('purchase.show', ['item'=>$item_id]);
    }

    // Stripe Checkout用
    public function checkout(Request $request, Item $item){
        $user = Auth::user();
        if (!$user->profile) {
            return back();
        }
        $selectedMethod = $request->input('payment_method', 'card');

        $allowedMethods = ['card', 'konbini'];
        if (!in_array($selectedMethod, $allowedMethods)) {
            $selectedMethod = 'card';
        }

        // Stripe初期化
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // セッション作成
        $session = StripeSession::create([
            'payment_method_types' => [$selectedMethod],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->item_name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('purchase.success', ['item' => $item->id]),
            'cancel_url' => route('purchase.cancel', ['item' => $item->id]),
        ]);

        // Stripe決済画面へリダイレクト
        return redirect($session->url);
    }

    // 支払い成功
    public function success(Item $item)
    {
        $user = Auth::user();

        // 商品ステータス更新
        $item->is_sold = true;
        $item->save();

        // 購入履歴保存
        Order::create([
            'profile_id' => $user->profile->id,
            'item_id' => $item->id,
            'address_id' => session('purchase_address_id'),
            'payment_method' => 'stripe',
        ]);

        return redirect()->route('mypage.index', ['page' => 'buy']);
    }

    // 支払いキャンセル
    public function cancel(Item $item)
    {
        return redirect()->route('purchase.show', ['item' => $item->id]);
    }

}
