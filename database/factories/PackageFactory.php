<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\PackageItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'photographer_id' => User::factory()->photographer(),
            'studio_id' => null,
            'package_name' => fake()->randomElement([
                'Gold Wedding',
                'Silver Portrait',
                'Bronze Event',
                'Family Studio',
                'Commercial Brand',
                'Outdoor Session',
                'Maternity Special',
                'Birthday Bash',
                'Corporate Headshots',
                'Graduation Celebration',
            ]) . ' Package',
            'description' => fake()->optional()->paragraph(),
            'price' => fake()->randomFloat(2, 50, 1500),
            'duration_hours' => fake()->randomElement([1.0, 2.0, 3.5, 4.0, 8.0]),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function withItems(int $count = 3): static
    {
        return $this->has(PackageItem::factory()->count($count), 'items');
    }
}

