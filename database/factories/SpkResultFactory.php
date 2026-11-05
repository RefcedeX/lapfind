<?php

namespace Database\Factories;

use App\Models\SpkResult;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpkResult>
 */
class SpkResultFactory extends Factory
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
            'title' => 'Rekomendasi Laptop - '.fake()->words(2, true),
            'weights_snapshot' => [
                'C1' => 0.25,
                'C2' => 0.20,
                'C3' => 0.15,
                'C4' => 0.15,
                'C5' => 0.10,
                'C6' => 0.15,
            ],
            'ranking_snapshot' => [
                [
                    'rank' => 1,
                    'alternative_id' => 1,
                    'name' => 'Laptop A',
                    'preference_score' => 0.7854,
                ],
            ],
            'steps_snapshot' => [],
        ];
    }
}
