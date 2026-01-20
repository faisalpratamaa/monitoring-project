<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Middleware\RoleMiddleware;

Route::middleware('guest')->group(function () {
    Volt::route('/login', 'auth/login')->name('login');
});

// Define the logout
Route::get('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
});

Route::middleware('auth')->group(function() {
    Volt::route('/', 'index');
    Volt::route('/profile', 'auth/profile');

    Route::middleware('role:1')->group(function () {
        Volt::route('/roles', 'roles.index');

        Volt::route('/users', 'users.index');
        Volt::route('/users/create', 'users.create');
        Volt::route('/users/{user}/edit', 'users.edit');
    });

    Route::middleware('role:1,2')->group(function () {
        Volt::route('/projects', 'projects.index');
        Volt::route('/projects/create', 'projects.create');
        Volt::route('/projects/{project}/edit', 'projects.edit');

        Volt::route('/tahapans', 'tahapans.index');
        Volt::route('/tahapans/create', 'tahapans.create');
        Volt::route('/tahapans/{tahapan}/edit', 'tahapans.edit');
    });
});

