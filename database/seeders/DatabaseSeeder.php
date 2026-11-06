<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a client only if the email does not already exist.
        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'full_name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'client',
            ]);
        }

        // Create a photographer only if the email does not already exist.
        if (! User::where('email', 'photographer@example.com')->exists()) {
            User::factory()->photographer()->create([
                'full_name' => 'Test Photographer',
                'email' => 'photographer@example.com',
            ]);
        }
    }
}
