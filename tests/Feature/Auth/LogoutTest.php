<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_session_is_invalidated_on_logout(): void
    {
        $user = User::factory()->create([
            'password' => 'SecurePass123!',
        ]);

        // Login first to establish a session.
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'SecurePass123!',
        ]);

        $this->assertAuthenticatedAs($user);
        $oldSessionId = session()->getId();

        // Logout
        $this->post('/logout');

        $this->assertGuest();
        $this->assertNotEquals($oldSessionId, session()->getId());
    }

    public function test_guest_cannot_post_to_logout(): void
    {
        $response = $this->post('/logout');

        $response->assertRedirect('/login');
    }
}

