<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Profile;

class ProfileTableSeeder extends Seeder
{
    public function run()
    {
        User::factory(10)->create()->each(function ($user) {
            Profile::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'profile_image_url' => null, // or 'sample/profile1.jpg'
                'postcode' => '100-0001',
                'address' => '東京都千代田区1-1-1',
                'building' => 'ビル101',
            ]);
        });
    }
}
