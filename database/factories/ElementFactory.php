<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Element>
 */
class ElementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'user_id' => User::factory(),
            'name' => $this->faker->unique()->word(),
            // 'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            // 'status' = fake()->randomElement(['A', 'C', 'H', 'X']),
        ];
    }
}
