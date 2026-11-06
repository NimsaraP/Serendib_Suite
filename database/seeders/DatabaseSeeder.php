<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\PackageItem;
use App\Models\Studio;
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

        $photographer = User::where('email', 'photographer@example.com')->first();

        // Retrieve or create a studio belonging to the photographer.
        $studio = Studio::firstOrCreate(
            ['photographer_id' => $photographer->id],
            [
                'studio_name' => 'Serendib Creative Studios',
                'description' => 'A boutique photography studio specializing in portraits, weddings, and commercial photography.',
                'location' => 'Colombo, Sri Lanka',
                'contact_email' => 'studio@serendibsuite.com',
                'contact_phone' => '0771234567',
            ]
        );

        // Realistic photography packages with varied prices, durations, and active status.
        $packagesData = [
            [
                'package_name' => 'Silver Portrait Package',
                'description' => 'Ideal for personal branding, graduations, or family studio portraits.',
                'price' => 150.00,
                'duration_hours' => 1.5,
                'is_active' => true,
                'items' => [
                    '1.5 hours in-studio session',
                    '15 fully retouched high-resolution digital photos',
                    'Online private gallery for download and sharing',
                    '2 wardrobe changes included',
                ],
            ],
            [
                'package_name' => 'Gold Wedding & Event Package',
                'description' => 'Comprehensive coverage for weddings, receptions, and milestone celebrations.',
                'price' => 750.00,
                'duration_hours' => 6.0,
                'is_active' => true,
                'items' => [
                    '6 hours of continuous event coverage',
                    'Two professional photographers for multi-angle coverage',
                    '100+ color-graded and retouched digital photos',
                    '1 premium hardcover layflat photo album (30 pages)',
                    'Drone aerial coverage of event venue',
                ],
            ],
            [
                'package_name' => 'Bronze Seasonal Mini Session',
                'description' => 'Quick outdoor mini photo session. Currently archived for the season.',
                'price' => 75.00,
                'duration_hours' => 0.5,
                'is_active' => false,
                'items' => [
                    '30 minutes express outdoor portrait session',
                    '5 professionally edited digital images',
                    'Digital delivery within 48 hours',
                ],
            ],
        ];

        foreach ($packagesData as $data) {
            $package = Package::firstOrCreate(
                [
                    'photographer_id' => $photographer->id,
                    'package_name' => $data['package_name'],
                ],
                [
                    'studio_id' => $studio->id,
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'duration_hours' => $data['duration_hours'],
                    'is_active' => $data['is_active'],
                ]
            );

            foreach ($data['items'] as $itemName) {
                PackageItem::firstOrCreate([
                    'package_id' => $package->id,
                    'item_name' => $itemName,
                ]);
            }
        }
    }
}
