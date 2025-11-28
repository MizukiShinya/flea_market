<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

class ProfileTableSeeder extends Seeder
{
    public function run()
    {
        $user = User::create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);
        Profile::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'profile_image_url' => null,
            'postcode' => '100-0001',
            'address' => '東京都千代田区1-1-1',
            'building' => 'ビル101',
        ]);
    }
}
