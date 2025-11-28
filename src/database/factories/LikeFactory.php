<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Like;
use App\Models\Profile;
use App\Models\Item;

class LikeFactory extends Factory
{
    protected $model = Like::class;

    public function definition()
    {
        return [
            'profile_id' => Profile::factory(),
            'item_id' => Item::factory(),
        ];
    }
}
