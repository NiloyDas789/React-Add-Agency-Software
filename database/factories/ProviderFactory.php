<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'                   => fake()->name(),
            'delivery_method'        => fake()->realTextBetween(10, 20),
            'response_type'          => fake()->realTextBetween(10, 20),
            'timezone'               => fake()->timezone(),
            'delivery_days'          => fake()->numberBetween(1, 60),
            'auto_delivery'          => fake()->boolean(),
            'file_naming_convention' => fake()->realTextBetween(10, 20),
            'contact_name'           => fake()->name(),
            'contact_email'          => fake()->safeEmail(),
        ];
    }
}
