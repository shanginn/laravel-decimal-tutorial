<?php

namespace Database\Factories;

use App\DataObjects\Money;
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
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'price' => Money::fromDecimal(
                (string) $this->faker->randomFloat(2, 1, 1000)
            ),
            'discount' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}
