<?php

namespace Database\Factories;

use App\Models\Criterion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Criterion>
 */
class CriterionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'C'.fake()->unique()->numberBetween(1, 99),
            'name' => fake()->word(),
            'type' => fake()->randomElement(['benefit', 'cost']),
            'unit' => fake()->randomElement(['Rp', 'GB', 'kg', 'skor 1-10', 'jam']),
            'description' => fake()->sentence(),
        ];
    }
}
