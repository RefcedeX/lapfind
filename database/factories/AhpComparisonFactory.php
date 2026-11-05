<?php

namespace Database\Factories;

use App\Models\AhpComparison;
use App\Models\Criterion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AhpComparison>
 */
class AhpComparisonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'criteria_a_id' => Criterion::factory(),
            'criteria_b_id' => Criterion::factory(),
            'value' => fake()->randomElement([1, 2, 3, 4, 5, 1 / 2, 1 / 3, 1 / 4, 1 / 5]),
        ];
    }
}
