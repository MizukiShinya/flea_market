<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Item;
use App\Models\Profile;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition()
    {
        return [
            'profile_id' => Profile::factory(),
            'item_name' => $this->faker->words(2, true),
            'brand' => $this->faker->company(),
            'detail' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 10000),
            'condition' => $this->faker->randomElement(['新品', '中古良品', '中古可']),
            'item_image_url' => null,
            'like_count' => 0,
        ];
    }
}
