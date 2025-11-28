<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Category;
use App\Models\Profile;
use App\Models\User;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品詳細ページに必要な情報が表示される()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $category1 = Category::factory()->create(['content' => '服']);
        $category2 = Category::factory()->create(['content' => 'アクセサリー']);

        $item = Item::factory()->create([
            'profile_id' => $profile->id,
            'item_name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 5000,
            'condition' => '新品',
            'detail' => 'テスト用の商品説明',
            'item_image_url' => 'item/test.jpg',
        ]);

        $item->categories()->sync([$category1->id, $category2->id]);

        $response = $this->actingAs($user)->get("/item/{$item->id}");

        $response->assertStatus(200);
        $response->assertViewIs('item.show');

        $response->assertSee('テスト商品');
        $response->assertSee('テストブランド');
        $response->assertSee('5,000');
        $response->assertSee('新品');
        $response->assertSee('テスト用の商品説明');
        $response->assertSee('item/test.jpg');

        $response->assertSee('服');
        $response->assertSee('アクセサリー');
    }

    /** @test */
    public function 存在しない商品IDの場合は404になる()
    {
        $response = $this->get('/item/99999');
        $response->assertStatus(404);
    }
}
