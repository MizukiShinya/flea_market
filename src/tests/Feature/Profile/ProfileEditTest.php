<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;

class ProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function ログインユーザーはプロフィール編集画面を表示できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->id,
            'name' => 'テストユーザー',
            'postcode' => '123-4567',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
        ]);

        $this->actingAs($user);

        $response = $this->get('/mypage/profile');

        $response->assertStatus(200);
        $response->assertSee('テストユーザー');
        $response->assertSee('123-4567');
        $response->assertSee('東京都渋谷区');
        $response->assertSee('テストビル');
    }

    /** @test */
    public function ログインユーザーはプロフィールを更新できる()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        Storage::fake('public');

        $response = $this->put('/mypage/update', [
            'name' => '更新ユーザー',
            'postcode' => '987-6543',
            'address' => '大阪市北区',
            'building' => '更新ビル',
            'profile_image_url' => null,
        ]);

        $response->assertRedirect('/');

        $profile->refresh();

        $this->assertEquals('更新ユーザー', $profile->name);
        $this->assertEquals('987-6543', $profile->postcode);
        $this->assertEquals('大阪市北区', $profile->address);
        $this->assertEquals('更新ビル', $profile->building);

        Storage::disk('public')->assertExists($profile->profile_image_url);
    }

    /** @test */
    public function 必須項目がない場合はバリデーションエラーになる()
    {
        $user = User::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->put('/mypage/update', [
            'name' => '',
            'postcode' => '',
            'address' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'postcode', 'address']);
    }

    /** @test */
    public function 未ログインユーザーはプロフィール編集できない()
    {
        $response = $this->get('/mypage/profile');
        $response->assertRedirect('/login');

        $response = $this->put('/mypage/update', [
            'name' => 'テスト',
            'postcode' => '123-4567',
            'address' => '東京都',
        ]);
        $response->assertRedirect('/login');
    }
}
