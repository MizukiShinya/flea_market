<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログアウトができる()
    {
        // まずユーザーを作成してログインさせる
        $user = User::factory()->create();

        // actingAs() でログイン状態にする
        $this->actingAs($user);

        // ログアウト実行
        $response = $this->post('/logout');

        // 認証情報がクリアされていること
        $this->assertGuest();

        // 遷移先（アプリの仕様に合わせて調整）
        // Breeze → '/'
        // フリマアプリならトップページ想定
        $response->assertRedirect('/');
    }
}
