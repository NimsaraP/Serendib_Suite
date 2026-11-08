<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // Successful login
    // ---------------------------------------------------------------

    public function test_client_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'role' => 'client',
            'password' => 'SecurePass123!',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'SecurePass123!',
        ]);

        $response->assertRedirect('/client/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_photographer_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->photographer()->create([
            'password' => 'SecurePass123!',
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'SecurePass123!',
        ]);

        $response->assertRedirect('/photographer/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    // ---------------------------------------------------------------
    // Invalid credentials
    // ---------------------------------------------------------------

    public function test_login_fails_with_incorrect_password(): void
    {
        $user = User::factory()->create([
            'password' => 'SecurePass123!',
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'WrongPassword!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'SomePassword!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // ---------------------------------------------------------------
    // Validation
    // ---------------------------------------------------------------

    public function test_login_requires_email(): void
    {
        $response = $this->from('/login')->post('/login', [
            'password' => 'SecurePass123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_requires_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'user@example.com',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    // ---------------------------------------------------------------
    // Session regeneration
    // ---------------------------------------------------------------

    public function test_session_is_regenerated_on_login(): void
    {
        $user = User::factory()->create([
            'password' => 'SecurePass123!',
        ]);

        $this->get('/login'); // start a session
        $oldSessionId = session()->getId();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'SecurePass123!',
        ]);

        $this->assertNotEquals($oldSessionId, session()->getId());
    }

    // ---------------------------------------------------------------
    // Rate limiting
    // ---------------------------------------------------------------

    public function test_login_is_rate_limited_after_too_many_attempts(): void
    {
        $user = User::factory()->create([
            'password' => 'SecurePass123!',
        ]);

        // Exhaust the rate limiter
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', [
                'email' => $user->email,
                'password' => 'WrongPassword!',
            ]);
        }

        // The 6th attempt should be throttled
        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'WrongPassword!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Verify the error message contains throttle language
        $errors = session('errors');
        $this->assertNotNull($errors);
        $this->assertStringContainsString('seconds', $errors->first('email'));
    }

    // ---------------------------------------------------------------
    // Guest middleware on auth routes
    // ---------------------------------------------------------------

    public function test_authenticated_user_cannot_access_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }
}

