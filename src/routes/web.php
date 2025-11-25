<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController, MyPageController, ItemController,LikeController, PurchaseController, CommentController};

// ゲストのみアクセス可能
Route::middleware('guest')->group(function(){
    Route::get('/register',[AuthController::class, 'create'])->name('register');
    Route::post('/register', [AuthController::class, 'store']);
    Route::get('/login',[AuthController::class,'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 商品一覧・詳細・検索
Route::get('/', [ItemController::class, 'index'])->name('item.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
Route::get('/search', [ItemController::class, 'search'])->name('item.search');

// ログイン後のみアクセス可能
Route::middleware('auth')->group(function () {
    //マイページ関連
    Route::prefix('mypage')->name('mypage.')->group(function () {
        Route::get('/', [MyPageController::class, 'index'])->name('index');
        Route::post('/', [MyPageController::class, 'store']);
        Route::get('/edit', [MyPageController::class, 'edit'])->name('edit');
        Route::put('/update', [MyPageController::class, 'update'])->name('update');
        Route::get('/purchase', [PurchaseController::class, 'orders'])->name('purchase');
    });

    // いいね・出品・コメント
    Route::prefix('item')->name('item.')->group(function () {
        Route::get('/mylist', [LikeController::class, 'index'])->name('mylist');
        Route::post('/{item}/like', [LikeController::class, 'toggle'])->name('like');
        Route::post('/{item}/comments',[CommentController::class, 'store'])->name('comments.store');
    });

    // 出品
    Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('item.store');

    // 商品購入
    Route::prefix('purchase')->name('purchase.')->group(function () {
        Route::get('/{item}', [PurchaseController::class, 'show'])->name('show');
        Route::post('/{item}', [PurchaseController::class, 'store'])->name('store');
        Route::get('/address/{item}', [PurchaseController::class, 'addressEdit'])->name('addressEdit');
        Route::put('address/{item}', [PurchaseController::class, 'addressUpdate'])->name('addressUpdate');
    });
});
