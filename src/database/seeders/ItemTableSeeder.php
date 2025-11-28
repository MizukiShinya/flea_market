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

        $items = [
            [
                'item_name' => '腕時計',
                'price' => 15000,
                'brand' => 'Rolax',
                'detail' => 'スタイリッシュなデザインのメンズ腕時計',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Armani+Mens+Clock.jpg',
                'condition' => '良好',
            ],
            [
                'item_name' => 'HDD',
                'price' => 5000,
                'brand' => '西芝',
                'detail' => '高速で信頼性の高いハードディスク',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/HDD+Hard+Disk.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'item_name' => '玉ねぎ3束',
                'price' => 300,
                'brand' => null,
                'detail' => '新鮮な玉ねぎ3束のセット',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/iLoveIMG+d.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'item_name' => '革靴',
                'price' => 4000,
                'brand' => null,
                'detail' => 'クラシックなデザインの革靴',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Leather+Shoes+Product+Photo.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'item_name' => 'ノートPC',
                'price' => 45000,
                'brand' => null,
                'detail' => '高性能なノートパソコン',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Living+Room+Laptop.jpg',
                'condition' => '良好',
            ],
            [
                'item_name' => 'マイク',
                'price' => 8000,
                'brand' => null,
                'detail' => '高音質のレコーディング用マイク',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Music+Mic+4632231.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
            [
                'item_name' => 'ショルダーバッグ',
                'price' => 3500,
                'brand' => null,
                'detail' => 'おしゃれなショルダーバッグ',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Purse+fashion+pocket.jpg',
                'condition' => 'やや傷や汚れあり',
            ],
            [
                'item_name' => 'タンブラー',
                'price' => 500,
                'brand' => null,
                'detail' => '使いやすいタンブラー',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Tumbler+souvenir.jpg',
                'condition' => '状態が悪い',
            ],
            [
                'item_name' => 'コーヒーミル',
                'price' => 4000,
                'brand' => 'Starbacks',
                'detail' => '手動のコーヒーミル',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/Waitress+with+Coffee+Grinder.jpg',
                'condition' => '良好',
            ],
            [
                'item_name' => 'メイクセット',
                'price' => 2500,
                'brand' => null,
                'detail' => '便利なメイクアップセット',
                'item_image_url' => 'https://coachtech-matter.s3.ap-northeast-1.amazonaws.com/image/%E5%A4%96%E5%87%BA%E3%83%A1%E3%82%A4%E3%82%AF%E3%82%A2%E3%83%83%E3%83%95%E3%82%9A%E3%82%BB%E3%83%83%E3%83%88.jpg',
                'condition' => '目立った傷や汚れなし',
            ],
        ];

        // シーディング
        foreach ($items as $item) {
            Item::create([
                'profile_id' => $profiles->random()->id,   // ランダムでプロフィール割当
                'category_id' => $categories->random()->id, // ランダムでカテゴリ割当
                'item_name' => $item['item_name'],
                'price' => $item['price'],
                'brand' => $item['brand'],
                'detail' => $item['detail'],
                'item_image_url' => $item['item_image_url'],
                'condition' => $item['condition'],
                'like_count' => rand(0, 20), // 任意でランダム
                'is_sold' => rand(0, 1),     // 任意でランダム
            ]);
        }
    }
}
