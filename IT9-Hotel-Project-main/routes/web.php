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
    Route::get('/employee', [\App\Http\Controllers\UsersController::class, 'index'])->name('users.index');
    Route::post('/employee', [\App\Http\Controllers\UsersController::class, 'store'])->name('users.store');
    Route::get('/guests', function () { return view('guests'); });
    Route::get('/reports', function () { return view('reports'); });
    Route::get('/reservations', [\App\Http\Controllers\ReservationController::class, 'index']);
    Route::post('/reservations', [\App\Http\Controllers\ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/rooms', function () { return view('rooms'); });
    Route::get('/settings', function () { return view('settings'); });
});

// Simple POC routes for auth pages (converted from React components)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
