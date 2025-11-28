<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Item;
use App\Models\Profile;
use App\Models\User;
use App\Models\Order;

class ItemPurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function ログインユーザーは商品を購入できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $item = Item::factory()->create();

        $this->actingAs($user);

        $response = $this->post("/purchase/{item}", [
            'payment_method' => 'credit',
            'address_id' => 1,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'profile_id' => $profile->id,
            'item_id' => $item->id,
            'payment_method' => 'credit',
        ]);
    }

    public function 未ログインユーザーは購入できない()
    {
        $item = Item::factory()->create();

        $response = $this->post("/purchase/{item}", [
            'payment_method' => 'credit',
            'address_id' => 1,
        ]);

        $response->assertRedirect('/login');
    }
}
