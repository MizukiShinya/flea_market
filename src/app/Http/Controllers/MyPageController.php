<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;
use App\Models\Order;

class MyPageController extends Controller
{
    public function index(Request $request){
        $user = Auth::user();
        $profile = $user->profile;
        $page = $request->query('page', 'sell');

        if ($page === 'buy') {

            // 購入した商品を取得
            $items = $profile->purchasedItems()->latest()->get();
        } else {
             // 出品した商品を取得
            $items = $profile->items()->latest()->get();
        }
        return view('mypage.index', compact('user', 'profile', 'items', 'page'));
    }
    // プロフィール設定
    public function edit(){
        $user = Auth::user();
        $profile = $user->profile;

        // 初回作成
        if (!$profile) {
            $profile = $user->profile()->create([
                'postcode' => '',
                'address' => '',
                'building' => '',
                'profile_image_url' => null,
            ]);}
        return view('mypage.edit', compact('profile', 'user'));
    }

    // プロフィール更新
    public function update(Request $request){
        $user = Auth::user();
        $profile = $user->profile;

        $validated = $request->validate([
            'bio' => 'nullable|string|max:255',
            'profile_image_url' => 'nullable|image|max:2048',
            'postcode' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
        ]);

        // 画像アップロード
        if ($request->hasFile('profile_image_url')) {
            // 古い画像を削除
            if ($profile->profile_image_url) {
                Storage::disk('public')->delete($profile->profile_image_url);
            }
            // 新しい画像を保存
            $validated['profile_image_url'] = $request->file('profile_image_url')->store('profile', 'public');
            }
        $profile->update($validated);
        return redirect()->route('item.index');
    }
    //購入履歴
    public function orders(){
        $profile_id = Auth::user()->profile->id;
        $orders = Order::with('item')
                ->where('profile_id', $profile_id)
                ->latest()
                ->get();
        return view('mypage.purchase', compact('orders'));
    }
}
