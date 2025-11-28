<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    
    protected $fillable = ['user_id', 'name', 'profile_image_url', 'postcode', 'address', 'building',];

    // ユーザーとの紐づけ
    public function user(){
        return $this->belongsTo(User::class);
    }

    // 出品した商品
    public function items(){
        return $this->hasMany(Item::class);
    }

    // お気に入り
    public function mylists(){
        return $this->hasMany(Favorite::class);
    }

    // 購入した商品一覧
    public function purchasedItems(){
        return $this->belongsToMany(Item::class, 'orders', 'profile_id', 'item_id')->withPivot('address_id', 'payment_method', 'created_at')->withTimestamps();
    }
    
}
