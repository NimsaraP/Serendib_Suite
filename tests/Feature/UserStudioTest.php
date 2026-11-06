<?php

namespace Tests\Feature;

use App\Models\Studio;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_studio_factory_creates_studio_with_photographer(): void
    {
        $studio = Studio::factory()->create();

        $this->assertDatabaseHas('studios', [
            'id' => $studio->id,
            'studio_name' => $studio->studio_name,
        ]);

        $this->assertNotNull($studio->photographer_id);
        $this->assertInstanceOf(User::class, $studio->photographer);
        $this->assertSame('photographer', $studio->photographer->role);
    }

    public function test_studio_belongs_to_photographer_relationship(): void
    {
        $photographer = User::factory()->photographer()->create();
        $studio = Studio::factory()->create([
            'photographer_id' => $photographer->id,
        ]);

        $this->assertTrue($studio->photographer->is($photographer));
    }

    public function test_photographer_can_have_multiple_studios(): void
    {
        $photographer = User::factory()->photographer()->create();

        $studios = Studio::factory()->count(3)->create([
            'photographer_id' => $photographer->id,
        ]);

        $this->assertCount(3, $photographer->studios);
        foreach ($studios as $studio) {
            $this->assertTrue($photographer->studios->contains($studio));
        }
    }

    public function test_database_seeder_is_idempotent(): void
    {
        // First run
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'full_name' => 'Test User',
            'role' => 'client',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'photographer@example.com',
            'full_name' => 'Test Photographer',
            'role' => 'photographer',
        ]);

        $this->assertSame(2, User::count());

        // Second run must not throw or duplicate records
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, User::count());
    }

    public function test_tests_use_isolated_sqlite_in_memory_database(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }
}
