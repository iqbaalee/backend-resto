<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        return [
            'order_date' => $this->faker->dateTimeBetween('-10 days', 'now'),
            'order_number' => $this->faker->unique()->numberBetween(100000, 999999),
            'customer_id' => $this->faker->numberBetween(1, 10),
            'down_payment' => $this->faker->numberBetween(100000, 1000000),
            'status' => $this->faker->randomElement(['paid', 'initial']),
        ];
    }
}
