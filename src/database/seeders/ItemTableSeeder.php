<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Category;

class ItemTableSeeder extends Seeder
{
   public function run()
    {
        $profiles = Profile::all();
        $categories = Category::all();

        foreach (range(1, 40) as $i) {
            Item::create([
                'profile_id' => $profiles->random()->id,
                'category_id' => $categories->random()->id,
                'item_name' => "サンプル商品 {$i}",
                'item_image_url' => 'sample/item' . rand(1, 5) . '.jpg', // 任意
                'condition' => collect(['新品', '未使用に近い', '目立った傷なし', 'やや傷あり'])->random(),
                'brand' => collect(['NIKE', 'UNIQLO', 'Apple', 'SONY', null])->random(),
                'price' => rand(500, 5000),
                'detail' => "これはサンプル商品 {$i} の説明文です。",
                'like_count' => rand(0, 20),
                'is_sold' => rand(0, 1),
            ]);
        }
    }
}
