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
    public function definition(): array
    {
        return [
            'order_status_id' => 1,
            'uuid' => fake()->uuid(),
            'products' => '{}',
            'address' => fake()->address(),
            'delivery_fee' => fake()->numberBetween(5, 10),
            'amount' => fake()->numberBetween(11, 1000),
            'payment_id' => null,
        ];
    }
}
