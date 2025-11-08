<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});


// Protected routes - only for authenticated users
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
    Route::get('/guests', function () { return view('guests'); });
    Route::get('/reports', function () { return view('reports'); });
    Route::get('/reservations', [\App\Http\Controllers\ReservationController::class, 'index']);
    Route::post('/reservations', [\App\Http\Controllers\ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/rooms', function () { return view('rooms'); });
    Route::get('/settings', function () { return view('settings'); });
});

// =============================================================================
// ADMIN-ONLY ROUTES
// =============================================================================
// These routes are restricted to users with the 'admin' role only.
// The 'admin' middleware checks:
// 1. User is authenticated
// 2. User has role = 'admin'
// 3. User account is active (is_active = true)
// =============================================================================
Route::middleware(['auth', 'admin'])->group(function () {
    // Employee Management - Admin only
    Route::get('/employee', [\App\Http\Controllers\UsersController::class, 'index'])->name('users.index');
    Route::post('/employee', [\App\Http\Controllers\UsersController::class, 'store'])->name('users.store');
    Route::delete('/employee/{user}', [\App\Http\Controllers\UsersController::class, 'destroy'])->name('users.destroy');
});

// =============================================================================
// RECEPTIONIST-ONLY ROUTES
// =============================================================================
// These routes are restricted to users with the 'receptionist' role only.
// The 'receptionist' middleware checks:
// 1. User is authenticated
// 2. User has role = 'receptionist'
// 3. User account is active (is_active = true)
// If any check fails, user will receive a 403 Unauthorized error.
// =============================================================================

// Method 1: Apply middleware to individual routes
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'receptionist']);

// Method 2: Apply middleware to a group of routes (RECOMMENDED)
Route::middleware(['auth', 'receptionist'])->group(function () {
    // Dashboard - Main receptionist interface
    Route::get('/receptionist/dashboard', function () {
        return view('dashboard');
    })->name('receptionist.dashboard');

    // Reservations - Manage hotel reservations
    Route::get('/receptionist/reservations', [\App\Http\Controllers\ReservationController::class, 'index'])
        ->name('receptionist.reservations');
    Route::post('/receptionist/reservations', [\App\Http\Controllers\ReservationController::class, 'store'])
        ->name('receptionist.reservations.store');

    // Rooms - View and manage room availability
    Route::get('/receptionist/rooms', function () {
        return view('rooms');
    })->name('receptionist.rooms');

    // Guests - Manage guest information
    Route::get('/receptionist/guests', function () {
        return view('guests');
    })->name('receptionist.guests');

    // Reports - View receptionist reports
    Route::get('/receptionist/reports', function () {
        return view('reports');
    })->name('receptionist.reports');

    // Settings - Receptionist account settings
    Route::get('/receptionist/settings', function () {
        return view('settings');
    })->name('receptionist.settings');
});

// Simple POC routes for auth pages (converted from React components)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
