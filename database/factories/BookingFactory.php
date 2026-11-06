<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => User::factory(),
            'package_id' => Package::factory(),
            'photographer_id' => function (array $attributes) {
                return isset($attributes['package_id'])
                    ? (Package::find($attributes['package_id'])?->photographer_id ?? User::factory()->photographer())
                    : User::factory()->photographer();
            },
            'studio_id' => function (array $attributes) {
                return isset($attributes['package_id'])
                    ? Package::find($attributes['package_id'])?->studio_id
                    : null;
            },
            'package_price' => function (array $attributes) {
                return isset($attributes['package_id'])
                    ? (Package::find($attributes['package_id'])?->price ?? 150.00)
                    : 150.00;
            },
            'event_date' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'event_location' => fake()->city() . ', ' . fake()->streetAddress(),
            'special_requirements' => fake()->optional()->sentence(),
            'booking_date' => now()->toDateString(),
            'booking_status' => 'pending',
            'event_status' => 'upcoming',
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => 'accepted',
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => 'rejected',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => 'cancelled',
            'event_status' => 'cancelled',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'booking_status' => 'accepted',
            'event_status' => 'completed',
        ]);
    }
}

