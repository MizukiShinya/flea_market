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

    public function ログインユーザーは商品を出品できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Storage::fake('public');

        $category1 = Category::factory()->create(['name' => '服']);
        $category2 = Category::factory()->create(['name' => 'アクセサリー']);

        $image = UploadedFile::fake()->image('test_item.jpg');

        $response = $this->post('/item', [
            'item_name' => 'テスト商品',
            'brand' => 'テストブランド',
            'detail' => 'テスト商品説明',
            'price' => 5000,
            'condition' => '新品',
            'categories' => [$category1->name, $category2->name],
            'image' => $image,
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

        Storage::disk('public')->assertExists($item->item_image_url);

        $this->assertEquals(2, $item->categories()->count());
    }

    public function 必須項目がない場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post('/item', [
            'item_name' => '',
            'price' => '',
            'condition' => '',
        ]);

        $response->assertSessionHasErrors(['item_name', 'price', 'condition']);
    }

    public function 画像が2MB以上の場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $image = UploadedFile::fake()->image('large.jpg')->size(3072);

        $response = $this->post('/item', [
            'item_name' => '商品名',
            'price' => 1000,
            'condition' => '新品',
            'image' => $image,
        ]);

        $response->assertSessionHasErrors(['image']);
    }

    public function 未ログインユーザーは商品を出品できない()
    {
        $response = $this->post('/item', [
            'item_name' => 'テスト商品',
            'price' => 1000,
            'condition' => '新品',
        ]);

        $response->assertRedirect('/login');
    }
}
