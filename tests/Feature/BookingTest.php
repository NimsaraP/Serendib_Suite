<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Package;
use App\Models\Studio;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_factory_creates_a_valid_booking(): void
    {
        $booking = Booking::factory()->create();

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'package_price' => $booking->package_price,
            'booking_status' => 'pending',
            'event_status' => 'upcoming',
        ]);

        $this->assertNotNull($booking->id);
        $this->assertNotNull($booking->event_location);
    }

    public function test_booking_belongs_to_a_client(): void
    {
        $client = User::factory()->create(['role' => 'client']);
        $booking = Booking::factory()->create(['client_id' => $client->id]);

        $this->assertInstanceOf(User::class, $booking->client);
        $this->assertTrue($booking->client->is($client));
        $this->assertTrue($client->clientBookings->contains($booking));
    }

    public function test_booking_belongs_to_a_photographer(): void
    {
        $photographer = User::factory()->photographer()->create();
        $booking = Booking::factory()->create(['photographer_id' => $photographer->id]);

        $this->assertInstanceOf(User::class, $booking->photographer);
        $this->assertTrue($booking->photographer->is($photographer));
        $this->assertTrue($photographer->photographerBookings->contains($booking));
    }

    public function test_booking_belongs_to_a_package(): void
    {
        $package = Package::factory()->create();
        $booking = Booking::factory()->create([
            'package_id' => $package->id,
            'photographer_id' => $package->photographer_id,
        ]);

        $this->assertInstanceOf(Package::class, $booking->package);
        $this->assertTrue($booking->package->is($package));
        $this->assertTrue($package->bookings->contains($booking));
    }

    public function test_booking_can_optionally_belong_to_a_studio(): void
    {
        $studio = Studio::factory()->create();
        $booking = Booking::factory()->create([
            'studio_id' => $studio->id,
            'photographer_id' => $studio->photographer_id,
        ]);

        $this->assertInstanceOf(Studio::class, $booking->studio);
        $this->assertTrue($booking->studio->is($studio));
        $this->assertTrue($studio->bookings->contains($booking));
    }

    public function test_booking_preserves_package_price_snapshot_when_package_price_changes(): void
    {
        $package = Package::factory()->create(['price' => 200.00]);

        $booking = Booking::factory()->create([
            'package_id' => $package->id,
            'package_price' => $package->price,
        ]);

        $this->assertSame('200.00', $booking->package_price);

        // Later, the photographer changes the package price in the catalog
        $package->update(['price' => 350.00]);

        // The historical booking must still preserve the original booked price
        $booking->refresh();
        $this->assertSame('200.00', $booking->package_price);
        $this->assertSame('350.00', $package->fresh()->price);
    }

    public function test_package_cannot_be_deleted_when_referenced_by_bookings(): void
    {
        $booking = Booking::factory()->create();

        $this->expectException(QueryException::class);
        $booking->package->delete();
    }

    public function test_client_cannot_be_deleted_when_referenced_by_bookings(): void
    {
        $booking = Booking::factory()->create();

        $this->expectException(QueryException::class);
        $booking->client->delete();
    }

    public function test_photographer_cannot_be_deleted_when_referenced_by_bookings(): void
    {
        $booking = Booking::factory()->create();

        $this->expectException(QueryException::class);
        $booking->photographer->delete();
    }

    public function test_studio_cannot_be_deleted_when_referenced_by_bookings(): void
    {
        $studio = Studio::factory()->create();
        $booking = Booking::factory()->create([
            'studio_id' => $studio->id,
            'photographer_id' => $studio->photographer_id,
        ]);

        $this->expectException(QueryException::class);
        $studio->delete();
    }

    public function test_booking_status_scopes_filter_correctly(): void
    {
        $pendingBooking = Booking::factory()->create(['booking_status' => 'pending']);
        $acceptedBooking = Booking::factory()->accepted()->create();

        $pendingResults = Booking::pending()->get();
        $acceptedResults = Booking::accepted()->get();

        $this->assertTrue($pendingResults->contains($pendingBooking));
        $this->assertFalse($pendingResults->contains($acceptedBooking));

        $this->assertTrue($acceptedResults->contains($acceptedBooking));
        $this->assertFalse($acceptedResults->contains($pendingBooking));
    }

    public function test_database_seeder_creates_expected_sample_booking_idempotently(): void
    {
        // First run
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1, Booking::count());
        $booking = Booking::first();
        $this->assertSame('accepted', $booking->booking_status);
        $this->assertSame('upcoming', $booking->event_status);
        $this->assertSame('150.00', $booking->package_price);
        $this->assertSame('Silver Portrait Package', $booking->package->package_name);

        // Second run must remain idempotent
        $this->seed(DatabaseSeeder::class);
        $this->assertSame(1, Booking::count());
    }
}

