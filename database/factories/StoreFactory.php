<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    protected $model = Store::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'postcode' => $this->faker->postcode,
            'status' => 'open',
            'type' => $this->faker->randomElement(['shop', 'restaurant', 'takeaway']),
            'location' => [
                'type' => 'Point',
                'coordinates' => [$this->faker->longitude, $this->faker->latitude],
            ],
            'max_delivery_distance' => $this->faker->numberBetween(1000, 10000),
        ];
    }
}
