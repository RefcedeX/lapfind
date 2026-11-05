<?php

namespace Database\Factories;

use App\Models\Alternative;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alternative>
 */
class AlternativeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $brands = ['ASUS', 'Lenovo', 'HP', 'Acer', 'Apple', 'Dell', 'MSI', 'Axioo'];
        $brand = fake()->randomElement($brands);

        return [
            'name' => $brand.' '.fake()->words(2, true),
            'brand' => $brand,
            'price' => fake()->numberBetween(6_000_000, 25_000_000),
            'image' => null,
            'description' => fake()->sentence(10),
        ];
    }
}
