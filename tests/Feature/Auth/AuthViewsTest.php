<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthViewsTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    // View Rendering Tests
    // ---------------------------------------------------------------

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Welcome to SerendibSuite');
        $response->assertSee('Email');
        $response->assertSee('Password');
        $response->assertSee('Remember me');
        $response->assertSee('Login');
        $response->assertSee('Register as Photographer');
        $response->assertSee('Register as Client');
        $response->assertSee(route('register.photographer'));
        $response->assertSee(route('register.client'));
    }

    public function test_client_registration_page_renders_successfully(): void
    {
        $response = $this->get('/register/client');

        $response->assertOk();
        $response->assertSee('Create your client account');
        $response->assertSee('First Name');
        $response->assertSee('Last Name');
        $response->assertSee('Email');
        $response->assertSee('Phone');
        $response->assertSee('Password');
        $response->assertSee('Confirm Password');
        $response->assertSee('Create Client Account');
        $response->assertSee(route('login'));
    }

    public function test_photographer_registration_page_renders_successfully(): void
    {
        $response = $this->get('/register/photographer');

        $response->assertOk();
        $response->assertSee('Create your photographer account');
        $response->assertSee('Photographer Portal');
        $response->assertSee('First Name');
        $response->assertSee('Last Name');
        $response->assertSee('Email');
        $response->assertSee('Phone');
        $response->assertSee('Password');
        $response->assertSee('Confirm Password');
        $response->assertSee('Create Photographer Account');
        $response->assertSee(route('login'));
    }

    // ---------------------------------------------------------------
    // Form Submission with First Name and Last Name
    // ---------------------------------------------------------------

    public function test_client_registration_with_first_and_last_name(): void
    {
        $response = $this->post('/register/client', [
            'first_name' => 'Nimsara',
            'last_name' => 'Perera',
            'email' => 'nimsara@example.com',
            'phone' => '0761234567',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertRedirect('/client/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'nimsara@example.com',
            'full_name' => 'Nimsara Perera',
            'role' => 'client',
        ]);
    }

    public function test_photographer_registration_with_first_and_last_name(): void
    {
        $response = $this->post('/register/photographer', [
            'first_name' => 'Chanuka',
            'last_name' => 'Praveen',
            'email' => 'sk@example.com',
            'phone' => '0760498597',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertRedirect('/photographer/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'sk@example.com',
            'full_name' => 'Chanuka Praveen',
            'role' => 'photographer',
        ]);
    }

    // ---------------------------------------------------------------
    // View Renders Validation Errors
    // ---------------------------------------------------------------

    public function test_login_view_displays_validation_error_on_invalid_credentials(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'invalid@example.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertRedirect('/login');

        // Follow redirect to assert view receives error
        $viewResponse = $this->followRedirects($response);
        $viewResponse->assertOk();
        $viewResponse->assertSee(__('auth.failed'));
    }

    public function test_registration_view_displays_validation_error_on_missing_required_fields(): void
    {
        $response = $this->from('/register/client')->post('/register/client', [
            'first_name' => '',
            'last_name' => '',
            'email' => 'not-an-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertRedirect('/register/client');

        $viewResponse = $this->followRedirects($response);
        $viewResponse->assertOk();
        $viewResponse->assertSee('The email field must be a valid email address.');
    }
}

