<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory()->accepted(),
            'client_id' => function (array $attributes) {
                if (isset($attributes['booking_id']) && is_numeric($attributes['booking_id'])) {
                    return Booking::find($attributes['booking_id'])?->client_id ?? User::factory()->create(['role' => 'client'])->id;
                }

                return User::factory()->create(['role' => 'client'])->id;
            },
            'invoice_number' => fake()->unique()->numerify('INV-2026-####'),
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'total_amount' => function (array $attributes) {
                if (isset($attributes['booking_id']) && is_numeric($attributes['booking_id'])) {
                    return Booking::find($attributes['booking_id'])?->package_price ?? 150.00;
                }

                return 150.00;
            },
            'amount_paid' => 0.00,
            'payment_status' => 'Unpaid',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'amount_paid' => 0.00,
            'payment_status' => 'Unpaid',
        ]);
    }

    public function partiallyPaid(?float $amount = null): static
    {
        return $this->state(fn () => [
            'amount_paid' => function (array $attributes) use ($amount) {
                $total = $attributes['total_amount'] ?? 150.00;
                if (is_callable($total)) {
                    $total = $total($attributes);
                }
                $total = (float) $total;
                $paid = $amount ?? round($total * 0.5, 2);

                return min($paid, $total);
            },
            'payment_status' => 'Partially Paid',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'amount_paid' => function (array $attributes) {
                $total = $attributes['total_amount'] ?? 150.00;
                if (is_callable($total)) {
                    $total = $total($attributes);
                }

                return (float) $total;
            },
            'payment_status' => 'Paid',
        ]);
    }
}
