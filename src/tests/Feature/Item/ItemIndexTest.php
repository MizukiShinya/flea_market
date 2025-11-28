<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Like;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 非ログイン時_全商品が表示される()
    {
        $items = Item::factory()->count(3)->create();

        $response = $this->get('/');

        $response->assertStatus(200);

        foreach ($items as $item) {
            $response->assertSee($item->item_name);
        }
    }

    /** @test */
    public function ログイン時_自分の出品商品は除外され他人の商品だけが表示される()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        $myItem = Item::factory()->create([
            'profile_id' => $profile->id,
            'item_name'  => '自分の商品',
        ]);

        $otherItem = Item::factory()->create([
            'item_name' => '他人の商品',
        ]);

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('他人の商品');
        $response->assertDontSee('自分の商品');
    }

    /** @test */
    public function ログイン時_tab_mylist指定で_いいねした商品のみが表示される()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
        ]);

        $likedItem = Item::factory()->create([
            'item_name' => 'いいね商品',
        ]);

        Like::create([
            'profile_id' => $profile->id,
            'item_id'    => $likedItem->id,
        ]);

        $notLikedItem = Item::factory()->create([
            'item_name' => 'いいねされていない商品',
        ]);

        $this->actingAs($user);

        $response = $this->get('/?tab=mylist');

        $response->assertStatus(200);
        $response->assertSee('いいね商品');
        $response->assertDontSee('いいねされていない商品');
    }
}