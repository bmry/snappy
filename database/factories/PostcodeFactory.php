<?php

namespace Database\Factories;

use App\Models\Postcode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Postcode>
 */
class PostcodeFactory extends Factory
{
    protected $model = Postcode::class;

    public function definition()
    {
        return [
            'postcode' => $this->faker->postcode,
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
            'country_id' => 1
        ];
    }
}
