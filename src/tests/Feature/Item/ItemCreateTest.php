<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Profile;
use App\Models\User;
use App\Models\Category;
use App\Models\Item;

class ItemCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログインユーザーは商品を出品できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $category1 = Category::factory()->create(['content' => '服']);
        $category2 = Category::factory()->create(['content' => 'アクセサリー']);

        $response = $this->post('/sell', [
            'item_name' => 'テスト商品',
            'brand' => 'テストブランド',
            'detail' => 'テスト商品説明',
            'price' => 5000,
            'condition' => '新品',
            'categories' => [$category1->content, $category2->content],
            'image' => null,
        ]);

        $item = Item::first();

        $response->assertRedirect(route('item.show', $item->id));

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'item_name' => 'テスト商品',
            'brand' => 'テストブランド',
            'detail' => 'テスト商品説明',
            'price' => 5000,
            'condition' => '新品',
            'profile_id' => $profile->id,
        ]);

        $this->assertEquals(2, $item->categories()->count());
    }

    public function 必須項目がない場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post('/sell', [
            'item_name' => '',
            'price' => '',
            'condition' => '',
        ]);

        $response->assertSessionHasErrors(['item_name', 'price', 'condition']);
    }

    public function 未ログインユーザーは商品を出品できない()
    {
        $response = $this->post('/sell', [
            'item_name' => 'テスト商品',
            'price' => 1000,
            'condition' => '新品',
        ]);

        $response->assertRedirect('/login');
    }
}
