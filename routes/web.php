<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Client Registration
    Route::get('/register/client', [RegisterController::class, 'showClientRegistrationForm'])
        ->name('register.client');
    Route::post('/register/client', [RegisterController::class, 'registerClient']);

    // Photographer Registration
    Route::get('/register/photographer', [RegisterController::class, 'showPhotographerRegistrationForm'])
        ->name('register.photographer');
    Route::post('/register/photographer', [RegisterController::class, 'registerPhotographer']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
