<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // Shared registration payload
    // ---------------------------------------------------------------

    /**
     * Build a valid registration payload.
     */
    protected function validPayload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Test User',
            'email' => 'testuser@example.com',
            'phone' => '0771234567',
            'address' => '123 Test Street, Colombo',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ], $overrides);
    }

    // ---------------------------------------------------------------
    // Successful registration
    // ---------------------------------------------------------------

    public function test_client_can_register_successfully(): void
    {
        $response = $this->post('/register/client', $this->validPayload());

        $response->assertRedirect('/client/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'role' => 'client',
            'full_name' => 'Test User',
        ]);
    }

    public function test_photographer_can_register_successfully(): void
    {
        $response = $this->post('/register/photographer', $this->validPayload([
            'email' => 'photographer@example.com',
        ]));

        $response->assertRedirect('/photographer/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'photographer@example.com',
            'role' => 'photographer',
        ]);
    }

    // ---------------------------------------------------------------
    // Password hashing
    // ---------------------------------------------------------------

    public function test_password_is_hashed_on_registration(): void
    {
        $this->post('/register/client', $this->validPayload());

        $user = User::where('email', 'testuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEquals('SecurePass123!', $user->password);
        $this->assertTrue(Hash::check('SecurePass123!', $user->password));
    }

    // ---------------------------------------------------------------
    // Role privilege escalation prevention
    // ---------------------------------------------------------------

    public function test_client_registration_ignores_role_in_request(): void
    {
        $this->post('/register/client', $this->validPayload([
            'role' => 'photographer',
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'role' => 'client',
        ]);
    }

    public function test_photographer_registration_ignores_role_in_request(): void
    {
        $this->post('/register/photographer', $this->validPayload([
            'email' => 'photographer@example.com',
            'role' => 'client',
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'photographer@example.com',
            'role' => 'photographer',
        ]);
    }

    public function test_client_registration_ignores_admin_role_in_request(): void
    {
        $this->post('/register/client', $this->validPayload([
            'role' => 'admin',
        ]));

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'role' => 'client',
        ]);
    }

    // ---------------------------------------------------------------
    // Duplicate email
    // ---------------------------------------------------------------

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->from('/register/client')->post('/register/client', $this->validPayload([
            'email' => 'taken@example.com',
        ]));

        $response->assertRedirect('/register/client');
        $response->assertSessionHasErrors('email');
    }

    // ---------------------------------------------------------------
    // Invalid email
    // ---------------------------------------------------------------

    public function test_registration_rejects_invalid_email(): void
    {
        $response = $this->from('/register/client')->post('/register/client', $this->validPayload([
            'email' => 'not-an-email',
        ]));

        $response->assertRedirect('/register/client');
        $response->assertSessionHasErrors('email');
    }

    // ---------------------------------------------------------------
    // Password confirmation mismatch
    // ---------------------------------------------------------------

    public function test_registration_rejects_password_mismatch(): void
    {
        $response = $this->from('/register/client')->post('/register/client', $this->validPayload([
            'password' => 'SecurePass123!',
            'password_confirmation' => 'DifferentPass!',
        ]));

        $response->assertRedirect('/register/client');
        $response->assertSessionHasErrors('password');
    }

    // ---------------------------------------------------------------
    // Required fields
    // ---------------------------------------------------------------

    public function test_registration_requires_full_name(): void
    {
        $response = $this->from('/register/client')->post('/register/client', $this->validPayload([
            'full_name' => '',
        ]));

        $response->assertRedirect('/register/client');
        $response->assertSessionHasErrors('full_name');
    }

    public function test_registration_requires_email(): void
    {
        $response = $this->from('/register/client')->post('/register/client', $this->validPayload([
            'email' => '',
        ]));

        $response->assertRedirect('/register/client');
        $response->assertSessionHasErrors('email');
    }

    public function test_registration_requires_password(): void
    {
        $response = $this->from('/register/client')->post('/register/client', $this->validPayload([
            'password' => '',
            'password_confirmation' => '',
        ]));

        $response->assertRedirect('/register/client');
        $response->assertSessionHasErrors('password');
    }

    // ---------------------------------------------------------------
    // Optional fields
    // ---------------------------------------------------------------

    public function test_registration_allows_nullable_phone(): void
    {
        $response = $this->post('/register/client', $this->validPayload([
            'phone' => null,
        ]));

        $response->assertRedirect('/client/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'phone' => null,
        ]);
    }

    public function test_registration_allows_nullable_address(): void
    {
        $response = $this->post('/register/client', $this->validPayload([
            'address' => null,
        ]));

        $response->assertRedirect('/client/dashboard');
        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'address' => null,
        ]);
    }

    // ---------------------------------------------------------------
    // Guest middleware
    // ---------------------------------------------------------------

    public function test_authenticated_user_cannot_access_registration(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register/client');

        $response->assertRedirect('/');
    }
}

