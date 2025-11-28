<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Profile;
use App\Models\User;

class ItemCommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログイン済みユーザーはコメントを送信できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/item/{$item->id}/comments", [
            'content' => 'テストコメント',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'profile_id' => $profile->id,
            'item_id' => $item->id,
            'content' => 'テストコメント',
        ]);
    }

    /** @test */
    public function コメントが入力されていない場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/item/{$item->id}/comments", [
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['content']);
    }

    /** @test */
    public function ログイン前のユーザーはコメントを送信できない()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comments", [
            'content' => 'テストコメント',
        ]);

        $response->assertRedirect('/login');
    }
}
