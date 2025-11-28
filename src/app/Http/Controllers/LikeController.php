<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // お気に入り追加・削除
    public function toggle($itemId)
    {
        $profile = Auth::user()->profile;
        $item = Item::findOrFail($itemId);

        // すでに登録されているか確認
        $existing = Like::where('profile_id', $profile->id)
            ->where('item_id', $item->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $status = 'removed';
            $liked = false;
        } else {
            // いいね追加
            Like::create([
                'profile_id' => $profile->id,
                'item_id' => $item->id,
            ]);
            $status = 'added';
            $liked = true;
        }
        // 最新のいいね数
        $likeCount = Like::where('item_id', $itemId)->count();

        // like_count カラムを更新したい場合
        $item->update(['like_count' => $likeCount]);

        return response()->json([
            'status' => $status,
            'count' => $likeCount,
            'liked' => $liked
        ]);
    }

    // 自分のお気に入り一覧
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }
        $user = Auth::user();
        $profile = $user->profile;

        if (!$profile) {
            abort(404, 'プロフィールが存在しません');
        }

        $keyword = session('search_keyword');
        $query = Mylist::with('item')
                ->where('profile_id', $profile->id);

        // 自分が出品した商品を除外
        $query->whereHas('item', function ($q) use ($user) {
            $q->where('user_id', '!=', $user->id);
        });
        // 検索キーワードがある場合は商品名で部分一致検索
        if (!empty($keyword)) {
            $query->whereHas('item', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            });
        }
        $mylists = $query->orderByDesc('created_at')->get();
            return view('item.mylist', compact('mylists', 'keyword'));
        }
        public function getLikedItems(){
            $likes = Like::where('user_id', Auth::id())
            ->with('item')->get();
        return $likes->pluck('item');
    }
}
