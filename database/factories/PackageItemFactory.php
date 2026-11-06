<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\PackageItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PackageItem>
 */
class PackageItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'package_id' => Package::factory(),
            'item_name' => fake()->randomElement([
                '50 high-resolution edited digital photos',
                'Full day event coverage (up to 8 hours)',
                '1 premium hardcover photo album (30 pages)',
                'Online private photo gallery with download access',
                'Professional 4K drone aerial photography',
                '2 framed canvas prints (16x24 inches)',
                'Pre-event consultation and location scouting',
                'Second photographer for complete multi-angle coverage',
                'High-end skin retouching and color grading',
                'Short highlight video reel (60 seconds for social media)',
            ]),
        ];
    }
}

