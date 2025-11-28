<?php

namespace Tests\Feature\Item;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Order;
use App\Models\Address;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    public function ログインユーザーは購入画面で支払い方法を選択できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $address = Address::factory()->create(['profile_id' => $profile->id]);
        $item = Item::factory()->create();

        $response = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'credit',
            'address_id' => $address->id,
        ]);

        $response->assertRedirect(); 

        $this->assertDatabaseHas('orders', [
            'profile_id' => $profile->id,
            'item_id' => $item->id,
            'payment_method' => 'credit',
            'address_id' => $address->id,
        ]);
    }

    public function 支払い方法を変更するとDBに反映される()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $address = Address::factory()->create(['profile_id' => $profile->id]);
        $item = Item::factory()->create();

        $this->post("/purchase/{$item->id}", [
            'payment_method' => 'credit',
            'address_id' => $address->id,
        ]);

        $order = Order::first();
        $this->assertEquals('credit', $order->payment_method);

        $this->post("/purchase/{$item->id}", [
            'payment_method' => 'konbini',
            'address_id' => $address->id,
        ]);

        $order = Order::latest()->first();
        $this->assertEquals('cash', $order->payment_method);
    }

    public function 未ログインユーザーは支払い方法を選択できない()
    {
        $item = Item::factory()->create();
        $address = Address::factory()->create();

        $response = $this->post("/purchase/{$item->id}", [
            'payment_method' => 'credit',
            'address_id' => $address->id,
        ]);

        $response->assertRedirect('/login');
    }
}
