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
        $tab = $request->query('tab', null); 

    if ($tab === 'mylist' && Auth::check()) {
        $profile = Auth::user()->profile;
        $likes = Like::where('profile_id', $profile->id)
                     ->with('item')
                     ->get();
        $items = $likes->pluck('item');
    } else {
        $query = Item::query();
        if (Auth::check()) {
            $query->where('profile_id', '!=', Auth::user()->profile->id);
        }
        $items = $query->orderBy('created_at', 'desc')->get();
    }

    return view('item.index', compact('items', 'tab'));
    }

    public function create(){
        $categories = Category::all();
        return view('item.create', compact('categories'));
    }

    public function store(Request $request){
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
        $item = new Item();
        $item->profile_id = Auth::user()->profile->id;
        $item->item_name = $validated['item_name'];
        $item->brand = $validated['brand'] ?? null;
        $item->detail = $validated['detail'] ?? null;
        $item->price = $validated['price'];
        $item->condition = $validated['condition'];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('item', 'public');
            $item->item_image_url = $path;
        }
        $item->save();

        if ($request->has('category_ids')) {
            $item->categories()->sync($request->category_ids);}

        elseif ($request->has('categories')) {
            $categoryIds = [];
        foreach ($request->categories as $categoryName) {
            $category = \App\Models\Category::firstOrCreate(['content' => $categoryName]);
            $categoryIds[] = $category->id;
        }
        $item->categories()->sync($categoryIds);
    }
        return redirect()->route('item.show', $item->id);
    }

    public function show($id){
        $item = Item::with('categories')->findOrFail($id);
        return view('item.show', compact('item'));
    }

    public function getMylist(){
        $profile = Auth::user()->profile;
        $mylists = Mylist::where('profile_id', $profile->id)
            ->with('item')->get();
        return view('item.mylist', compact('mylists'));
    }

    public function search(Request $request){
    $keyword = $request->input('keyword');

    if (!empty($keyword)) {
        session(['search_keyword' => $keyword]);
    } else {
        session()->forget('search_keyword');
    }
    $query = Item::query();

    if (!empty($keyword)) {
        $query->where('item_name', 'like', "%{$keyword}%");
    }
    if (auth()->check()) {
        $query->where('profile_id', '!=', Auth::user()->profile->id);
    }
    $items = $query->orderBy('created_at', 'desc')->get();
    $tab = 'all';
    return view('item.index', compact('items', 'keyword', 'tab'));
    }
}
