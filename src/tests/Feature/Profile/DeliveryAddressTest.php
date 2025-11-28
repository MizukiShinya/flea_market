<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Address;
use App\Models\Item;
use App\Models\Order;

class DeliveryAddressTest extends TestCase
{
    use RefreshDatabase;

    public function ログインユーザーは配送先住所を登録できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->post('/addresses', [
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
        ]);

        $response->assertRedirect('/profile/addresses');

        $this->assertDatabaseHas('addresses', [
            'profile_id' => $profile->id,
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
        ]);
    }

    public function 登録した配送先は購入画面に反映される()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $address = Address::factory()->create([
            'profile_id' => $profile->id,
            'postcode' => '987-6543',
            'address' => '大阪市北区',
            'building' => '購入ビル',
        ]);

        $item = Item::factory()->create();

        $response = $this->get("/item/{$item->id}/purchase");
        $response->assertStatus(200);

        $response->assertSee('987-6543');
        $response->assertSee('大阪市北区');
        $response->assertSee('購入ビル');
    }

    public function 購入した商品に配送先住所が紐づいて保存される()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $address = Address::factory()->create([
            'profile_id' => $profile->id,
        ]);

        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/purchase", [
            'payment_method' => 'credit',
            'address_id' => $address->id,
        ]);

        $response->assertRedirect(); 

        $this->assertDatabaseHas('orders', [
            'profile_id' => $profile->id,
            'item_id' => $item->id,
            'address_id' => $address->id,
            'payment_method' => 'credit',
        ]);
    }

    public function 未ログインユーザーは配送先変更画面にアクセスできない()
    {
        $response = $this->get('/addresses');
        $response->assertRedirect('/login');

        $response = $this->post('/addresses', [
            'postcode' => '123-4567',
            'address' => '東京都',
        ]);
        $response->assertRedirect('/login');
    }
}
