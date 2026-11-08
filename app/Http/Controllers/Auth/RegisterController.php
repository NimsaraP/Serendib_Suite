<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    /**
     * Show the client registration form.
     */
    public function showClientRegistrationForm()
    {
        return view('auth.register-client');
    }

    /**
     * Show the photographer registration form.
     */
    public function showPhotographerRegistrationForm()
    {
        return view('auth.register-photographer');
    }

    /**
     * Handle a client registration request.
     */
    public function registerClient(RegisterRequest $request): RedirectResponse
    {
        $user = $this->createUser($request, 'client');

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/client/dashboard');
    }

    /**
     * Handle a photographer registration request.
     */
    public function registerPhotographer(RegisterRequest $request): RedirectResponse
    {
        $user = $this->createUser($request, 'photographer');

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/photographer/dashboard');
    }

    /**
     * Create a new user with the given role.
     *
     * The role is set explicitly on the server side and never accepted
     * from the request payload, preventing privilege escalation.
     */
    protected function createUser(RegisterRequest $request, string $role): User
    {
        $user = new User();
        $user->full_name = $request->validated('full_name');
        $user->email = $request->validated('email');
        $user->phone = $request->validated('phone');
        $user->address = $request->validated('address');
        $user->password = $request->validated('password');
        $user->role = $role;
        $user->save();

        return $user;
    }
}

