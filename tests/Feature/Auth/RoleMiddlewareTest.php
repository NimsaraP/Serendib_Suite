<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Register temporary test routes before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Provide a named 'login' route so guest redirects work.
        Route::get('/login', fn () => 'login-page')->name('login');

        // Routes guarded by individual roles.
        Route::middleware('role:client')->get('/client-area', fn () => 'client-ok');
        Route::middleware('role:photographer')->get('/photographer-area', fn () => 'photographer-ok');

        // Route allowing multiple roles.
        Route::middleware('role:client,photographer')->get('/shared-area', fn () => 'shared-ok');
    }

    // ---------------------------------------------------------------
    // Role helper tests
    // ---------------------------------------------------------------

    public function test_is_client_returns_true_for_client(): void
    {
        $user = User::factory()->create(['role' => 'client']);

        $this->assertTrue($user->isClient());
        $this->assertFalse($user->isPhotographer());
    }

    public function test_is_photographer_returns_true_for_photographer(): void
    {
        $user = User::factory()->photographer()->create();

        $this->assertTrue($user->isPhotographer());
        $this->assertFalse($user->isClient());
    }

    public function test_role_helpers_return_false_for_other_roles(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->assertFalse($user->isClient());
        $this->assertFalse($user->isPhotographer());
    }

    // ---------------------------------------------------------------
    // Middleware – guest behaviour
    // ---------------------------------------------------------------

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/client-area');

        $response->assertRedirect('/login');
    }

    // ---------------------------------------------------------------
    // Middleware – authorised access
    // ---------------------------------------------------------------

    public function test_client_can_access_client_route(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->get('/client-area');

        $response->assertOk();
        $response->assertSee('client-ok');
    }

    public function test_photographer_can_access_photographer_route(): void
    {
        $photographer = User::factory()->photographer()->create();

        $response = $this->actingAs($photographer)->get('/photographer-area');

        $response->assertOk();
        $response->assertSee('photographer-ok');
    }

    // ---------------------------------------------------------------
    // Middleware – forbidden access (403)
    // ---------------------------------------------------------------

    public function test_client_is_forbidden_from_photographer_route(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->get('/photographer-area');

        $response->assertForbidden();
    }

    public function test_photographer_is_forbidden_from_client_route(): void
    {
        $photographer = User::factory()->photographer()->create();

        $response = $this->actingAs($photographer)->get('/client-area');

        $response->assertForbidden();
    }

    // ---------------------------------------------------------------
    // Middleware – multi-role route
    // ---------------------------------------------------------------

    public function test_client_can_access_shared_route(): void
    {
        $client = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($client)->get('/shared-area');

        $response->assertOk();
        $response->assertSee('shared-ok');
    }

    public function test_photographer_can_access_shared_route(): void
    {
        $photographer = User::factory()->photographer()->create();

        $response = $this->actingAs($photographer)->get('/shared-area');

        $response->assertOk();
        $response->assertSee('shared-ok');
    }

    public function test_unknown_role_is_forbidden_from_shared_route(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($user)->get('/shared-area');

        $response->assertForbidden();
    }
}
