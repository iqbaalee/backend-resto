<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'price' => $this->faker->randomFloat(2, 1, 1000),
            'stock' => $this->faker->numberBetween(0, 5),
            'type' => $this->faker->randomElement(['table', 'meal']),
            'description' => $this->faker->text,
            'photo' => $this->faker->text,
            'capacity' => $this->faker->numberBetween(1, 10),
        ];
    }
}
