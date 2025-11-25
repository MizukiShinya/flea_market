<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'profile_id',
        'category_id',
        'item_name',
        'item_image_url',
        'condition',
        'brand',
        'price',
        'detail',
        'like_count',
        'is_sold',
    ];

    protected $casts = [
        'is_sold' => 'boolean',
    ];
    
    // 出品者
    public function profile(){
        return $this->belongsTo(Profile::class,);
    }
    // カテゴリー
    public function category(){
        return $this->belongsTo(Category::class, 'category_id');
    }
    // いいね機能
    public function Likes(){
        return $this->hasMany(Like::class, 'item_id');
    }
    public function isLikedBy($profile){
        return  $profile ? $this->likes()->where('profile_id', $profile->id)->exists() : false;
    }
    // コメント
    public function comments(){
        return $this->hasMany(Comment::class, 'item_id');
    }
    // カテゴリー
    public function categories(){
        return $this->belongsToMany(Category::class, 'category_item');
    }
    // 購入済み
    public function order(){
        return $this->hasOne(Order::class);
    }
}
