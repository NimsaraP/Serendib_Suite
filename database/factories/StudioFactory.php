<?php

namespace Database\Factories;

use App\Models\Studio;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Studio>
 */
class StudioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'photographer_id' => User::factory()->photographer(),
            'studio_name' => fake()->company(),
            'description' => fake()->optional()->paragraph(),
            'location' => fake()->city(),
            'contact_email' => fake()->unique()->safeEmail(),
            'contact_phone' => fake()->optional()->numerify('##########'),
        ];
    }
}

