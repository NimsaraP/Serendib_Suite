<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PackageItem;
use App\Models\Studio;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    public function test_package_factory_creates_a_valid_package(): void
    {
        $package = Package::factory()->create();

        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'package_name' => $package->package_name,
        ]);

        $this->assertNotNull($package->id);
        $this->assertNotNull($package->package_name);
        $this->assertTrue($package->is_active);
    }

    public function test_package_factory_creates_a_package_associated_with_a_photographer(): void
    {
        $package = Package::factory()->create();

        $this->assertNotNull($package->photographer_id);
        $this->assertInstanceOf(User::class, $package->photographer);
        $this->assertSame('photographer', $package->photographer->role);
    }

    public function test_package_can_optionally_belong_to_a_studio(): void
    {
        // Default package has null studio_id
        $packageWithoutStudio = Package::factory()->create();
        $this->assertNull($packageWithoutStudio->studio_id);
        $this->assertNull($packageWithoutStudio->studio);

        // Package explicitly associated with a studio
        $studio = Studio::factory()->create();
        $packageWithStudio = Package::factory()->create([
            'photographer_id' => $studio->photographer_id,
            'studio_id' => $studio->id,
        ]);

        $this->assertNotNull($packageWithStudio->studio_id);
        $this->assertInstanceOf(Studio::class, $packageWithStudio->studio);
        $this->assertTrue($packageWithStudio->studio->is($studio));
    }

    public function test_photographer_can_have_multiple_packages(): void
    {
        $photographer = User::factory()->photographer()->create();

        $packages = Package::factory()->count(3)->create([
            'photographer_id' => $photographer->id,
        ]);

        $this->assertCount(3, $photographer->packages);
        foreach ($packages as $package) {
            $this->assertTrue($photographer->packages->contains($package));
        }
    }

    public function test_package_can_have_multiple_package_items(): void
    {
        $package = Package::factory()->withItems(3)->create();

        $this->assertCount(3, $package->items);
        foreach ($package->items as $item) {
            $this->assertInstanceOf(PackageItem::class, $item);
            $this->assertSame($package->id, $item->package_id);
        }
    }

    public function test_package_item_belongs_to_the_correct_package(): void
    {
        $package = Package::factory()->create();
        $item = PackageItem::factory()->create([
            'package_id' => $package->id,
        ]);

        $this->assertInstanceOf(Package::class, $item->package);
        $this->assertTrue($item->package->is($package));
    }

    public function test_active_scope_returns_only_active_packages(): void
    {
        $activePackage = Package::factory()->create(['is_active' => true]);
        $inactivePackage = Package::factory()->inactive()->create();

        $activePackages = Package::active()->get();

        $this->assertTrue($activePackages->contains($activePackage));
        $this->assertFalse($activePackages->contains($inactivePackage));
    }

    public function test_inactive_factory_state_creates_inactive_packages(): void
    {
        $package = Package::factory()->inactive()->create();

        $this->assertFalse($package->is_active);
        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'is_active' => false,
        ]);
    }

    public function test_package_price_and_duration_casts_work_correctly(): void
    {
        $package = Package::factory()->create([
            'price' => 199.99,
            'duration_hours' => 3.5,
            'is_active' => true,
        ]);

        // Decimal cast formats to 2 decimal places as string
        $this->assertSame('199.99', $package->price);

        // Duration cast converts to float
        $this->assertIsFloat($package->duration_hours);
        $this->assertSame(3.5, $package->duration_hours);

        // is_active cast converts to boolean
        $this->assertIsBool($package->is_active);
        $this->assertTrue($package->is_active);
    }

    public function test_database_seeder_creates_expected_sample_records(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, User::count());
        $this->assertSame(1, Studio::count());
        $this->assertSame(3, Package::count());
        $this->assertSame(12, PackageItem::count());

        $this->assertDatabaseHas('packages', [
            'package_name' => 'Silver Portrait Package',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('packages', [
            'package_name' => 'Gold Wedding & Event Package',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('packages', [
            'package_name' => 'Bronze Seasonal Mini Session',
            'is_active' => false,
        ]);

        $silver = Package::where('package_name', 'Silver Portrait Package')->first();
        $this->assertCount(4, $silver->items);

        $gold = Package::where('package_name', 'Gold Wedding & Event Package')->first();
        $this->assertCount(5, $gold->items);

        $bronze = Package::where('package_name', 'Bronze Seasonal Mini Session')->first();
        $this->assertCount(3, $bronze->items);
    }

    public function test_database_seeder_is_idempotent_when_executed_multiple_times(): void
    {
        // First execution
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, User::count());
        $this->assertSame(1, Studio::count());
        $this->assertSame(3, Package::count());
        $this->assertSame(12, PackageItem::count());

        // Second execution must not duplicate records or throw constraint violations
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(2, User::count());
        $this->assertSame(1, Studio::count());
        $this->assertSame(3, Package::count());
        $this->assertSame(12, PackageItem::count());
    }

    public function test_photographer_cannot_be_deleted_when_referenced_by_packages(): void
    {
        $photographer = User::factory()->photographer()->create();
        Package::factory()->create([
            'photographer_id' => $photographer->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $photographer->delete();
    }

    public function test_studio_cannot_be_deleted_when_referenced_by_packages(): void
    {
        $studio = Studio::factory()->create();
        Package::factory()->create([
            'photographer_id' => $studio->photographer_id,
            'studio_id' => $studio->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        $studio->delete();
    }

    public function test_deleting_a_package_cascades_to_its_items(): void
    {
        $package = Package::factory()->withItems(3)->create();
        $this->assertSame(3, PackageItem::count());

        $package->delete();

        $this->assertSame(0, PackageItem::count());
    }
}

