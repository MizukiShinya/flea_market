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
    public function show($item_id){
        $item = Item::findOrFail($item_id);
        $user = Auth::user()->load('profile');

        if ($item->seller_id === Auth::id()) {
            return redirect()->route('item.show', $item->id);}

        if ($item->is_sold) {
            return redirect()->route('item.show', $item->id);}
        
        $purchaseAddress = Address::where('profile_id', $user->profile->id)->first();

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

    public function store(Request $request, Item $item){
        $user = Auth::user();
        if (!$user->profile) {
            return back()->with('error', 'プロフィールが登録されていません。');
        }

        $profileId = $user->profile->id;
        $addressId = $request->input('address_id');
        $paymentMethod = $request->input('payment_method', '未選択');

        $paymentMethod = $paymentMethod === 'konbini' ? 'cash' : $paymentMethod;

        $order =Order::create([
            'profile_id' => $user->profile->id,
            'item_id' => $item->id,
            'address_id' => $addressId,
            'payment_method' => $paymentMethod,
        ]);
        $item->is_sold = true;
        $item->save();
        return redirect()->route('mypage.index', ['page' => 'buy']);
    }
    public function addressEdit($item_id)
    {
        $profileId = Auth::user()->profile->id;
        $address = Address::where('profile_id', $profileId)->first();
        $item = Item::findOrFail($item_id);
        return view('purchase.address', compact('address', 'item'));
    }

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
        $order = $profile->orders()->create([
            'item_id' => $item->id,
            'payment_method' => $selectedMethod,
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

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

        return redirect($session->url);
    }

    public function success(Item $item)
    {
        $user = Auth::user();

        $item->is_sold = true;
        $item->save();

        Order::create([
            'profile_id' => $user->profile->id,
            'item_id' => $item->id,
            'address_id' => session('purchase_address_id'),
            'payment_method' => 'stripe',
        ]);

        return redirect()->route('mypage.index', ['page' => 'buy']);
    }

    public function cancel(Item $item)
    {
        return redirect()->route('purchase.show', ['item' => $item->id]);
    }

}
