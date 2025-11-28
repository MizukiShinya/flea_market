<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use App\Models\Like;

class ItemDetailLikeTest extends TestCase
{
    use RefreshDatabase;

    public function 商品詳細ページにいいねボタンが表示される()
    {
        $profile = Profile::factory()->create();
        $item = Item::factory()->create(['profile_id' => $profile->id]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee('like');
    }

    public function ログインユーザーはいいねを追加できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson("/item/{item}/like");

        $response->assertJson([
            'status' => 'added',
            'liked' => true,
            'count' => 1
        ]);

        $this->assertDatabaseHas('likes', [
            'profile_id' => $user->profile->id,
            'item_id' => $item->id
        ]);
    }

    public function ログインユーザーは既存のいいねを削除できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create();

        Like::factory()->create([
            'profile_id' => $profile->id,
            'item_id' => $item->id
        ]);

        $this->actingAs($user);

        $response = $this->postJson("/item/{item}/like");

        $response->assertJson([
            'status' => 'removed',
            'liked' => false,
            'count' => 0
        ]);

        $this->assertDatabaseMissing('likes', [
            'profile_id' => $profile->id,
            'item_id' => $item->id
        ]);
    }
}
