<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RestrictedAni>
 */
class RestrictedAniFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'restricted_ani' => fake()->phoneNumber(),
            'date'           => fake()->date(),
            'reason'         => fake()->realTextBetween(10, 20),
        ];
    }
}
