<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Category;
use App\Models\Like;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', null); // ?tab=mylist の場合のみセット

    if ($tab === 'mylist' && Auth::check()) {
        // ログインユーザーのいいね商品だけ取得
        $profile = Auth::user()->profile;
        $likes = Like::where('profile_id', $profile->id)
                     ->with('item')
                     ->get();
        $items = $likes->pluck('item');
    } else {
        // 全商品（自分以外の出品）を取得
        $query = Item::query();
        if (Auth::check()) {
            $query->where('profile_id', '!=', Auth::user()->profile->id);
        }
        $items = $query->orderBy('created_at', 'desc')->get();
    }

    return view('item.index', compact('items', 'tab'));
    }

    // 出品フォーム表示
    public function create(){
        $categories = Category::all();
        return view('item.create', compact('categories'));
    }

    // 商品出品
    public function store(Request $request){
        // バリデーション
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'detail' => 'nullable|string',
            'price' => 'required|integer|min:0',
            'condition' => 'required|string|max:255',
            'categories' => 'array',
            'categories.*' => 'string',
            'image' => 'nullable|image|max:2048',
        ]);
        // 新しい商品インスタンス作成
        $item = new Item();
        $item->profile_id = Auth::user()->profile->id;
        $item->item_name = $validated['item_name'];
        $item->brand = $validated['brand'] ?? null;
        $item->detail = $validated['detail'] ?? null;
        $item->price = $validated['price'];
        $item->condition = $validated['condition'];

        // 画像アップロード処理
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('item', 'public');
            $item->item_image_url = $path;
        }
        $item->save();

        // 複数カテゴリ登録（中間テーブル）
        // Blade側で name="category_ids[]" にしている場合
        if ($request->has('category_ids')) {
            $item->categories()->sync($request->category_ids);}

        // Blade側で name="categories[]" にカテゴリ名を送っている場合
        elseif ($request->has('categories')) {
            $categoryIds = [];
        foreach ($request->categories as $categoryName) {
            $category = \App\Models\Category::firstOrCreate(['name' => $categoryName]);
            $categoryIds[] = $category->id;
        }
        $item->categories()->sync($categoryIds);
    }
        return redirect()->route('item.show', $item->id);
    }

    // 商品詳細ページ
    public function show($id){
        $item = Item::with('categories')->findOrFail($id);
        return view('item.show', compact('item'));
    }

    // お気に入り一覧を取得
    public function getMylist(){
        $profile = Auth::user()->profile;
        $mylists = Mylist::where('profile_id', $profile->id)
            ->with('item')->get();
        return view('item.mylist', compact('mylists'));
    }

    // 検索欄
    public function search(Request $request){
    $keyword = $request->input('keyword');

    // セッションにキーワードを保存
    if (!empty($keyword)) {
        session(['search_keyword' => $keyword]);
    } else {
        session()->forget('search_keyword');
    }
    $query = Item::query();

    // 商品名で部分一致検索
    if (!empty($keyword)) {
        $query->where('item_name', 'like', "%{$keyword}%");
    }
    // ログイン中なら自分の商品を除外
    if (auth()->check()) {
        $query->where('profile_id', '!=', Auth::user()->profile->id);
    }
    // 並び順は最新順
    $items = $query->orderBy('created_at', 'desc')->paginate(12);
    return view('item.index', compact('items', 'keyword'));
    }
}
