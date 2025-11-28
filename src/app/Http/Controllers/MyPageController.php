<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\ItemController;
use App\Models\Profile;
use App\Models\Order;

class MyPageController extends Controller
{
    public function index(Request $request, ItemController $itemController){
        $user = Auth::user();
        $profile = $user->profile;
        $page = $request->query('page');

        if ($page === 'buy') {
            $items = $profile->purchasedItems()->latest()->get();
        } elseif ($page === 'mylist') {
        $response = $itemController->mylist();
        $mylists = $response->getData()['mylists'];
        $items = $mylists->pluck('item');
        } else {
            $items = $profile->items()->latest()->get();
            $page = 'sell';
        }
        return view('mypage.index', compact('user', 'profile', 'items', 'page',));
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
        return view('mypage.profile', compact('profile', 'user'));
    }

    // プロフィール更新
    public function update(Request $request){
        $user = Auth::user();
        $profile = $user->profile;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:255',
            'profile_image_url' => 'nullable|image|max:2048',
            'postcode' => 'required|string|max:20',
            'address' => 'required|string|max:255',
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
