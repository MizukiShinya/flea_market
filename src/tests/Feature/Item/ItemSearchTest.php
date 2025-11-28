<?php

namespace Tests\Feature\Item;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品名にキーワードが含まれる商品だけが表示される()
    {
        $match1 = Item::factory()->create(['item_name' => 'Apple Watch']);
        $match2 = Item::factory()->create(['item_name' => 'Apple AirPods']);
        $noMatch = Item::factory()->create(['item_name' => 'Samsung Galaxy']);

        $response = $this->get('/search?keyword=Apple');

        $response->assertSee('Apple Watch');
        $response->assertSee('Apple AirPods');

        $response->assertDontSee('Samsung Galaxy');

        $response->assertViewIs('item.index');
    }

    /** @test */
    public function ログイン中は自分が出品した商品が除外される()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $mine = Item::factory()->create([
            'profile_id' => $profile->id,
            'item_name' => 'My Item',
        ]);

        $otherProfile = Profile::factory()->create();
        $other = Item::factory()->create([
            'profile_id' => $otherProfile->id,
            'item_name' => 'Other Item'
        ]);

        $this->actingAs($user);

        $response = $this->get('/search?keyword=Item');

        $response->assertDontSee('My Item');
        $response->assertSee('Other Item');
    }

    /** @test */
    public function 検索キーワードが空のときは全件表示されるが自分の商品は除外される()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $mine = Item::factory()->create([
            'profile_id' => $profile->id,
            'item_name' => 'Mine'
        ]);

        $otherProfile = Profile::factory()->create();
        $other = Item::factory()->create([
            'profile_id' => $otherProfile->id,
            'item_name' => 'Other'
        ]);

        $this->actingAs($user);

        $response = $this->get('/search?keyword=');

        $response->assertDontSee('Mine');
        $response->assertSee('Other');
    }

    /** @test */
    public function 検索結果が新しい順になっている()
    {
        $old = Item::factory()->create([
        'item_name' => 'Old Item',
        'created_at' => now()->subDays(5),
    ]);

    $new = Item::factory()->create([
        'item_name' => 'New Item',
        'created_at' => now(),
    ]);

    $response = $this->get('/search?keyword=Item');

    $response->assertStatus(200);
    $response->assertViewIs('item.index');

    $items = $response->viewData('items');

    $this->assertEquals('New Item', $items->first()->item_name);
    $this->assertEquals('Old Item', $items->last()->item_name);
    }
}
