<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes
Route::get('/', function () {
    return view('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =============================================================================
// PROTECTED ROUTES - AUTHENTICATED USERS ONLY
// =============================================================================
Route::middleware('auth')->group(function () {
    
    // Dashboard Routes - Accessible to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Transactions/Guests Routes - Dynamic with controller
    Route::get('/guests', [TransactionController::class, 'index'])->name('guests');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    
    // Static pages for all authenticated users
    Route::get('/reports', function () { 
        return view('reports'); 
    })->name('reports');
    
    Route::get('/settings', function () { 
        return view('settings'); 
    })->name('settings');
    
    // Reservations - Accessible to all authenticated users
    Route::prefix('reservations')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/', [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::post('/confirm/{reservation}', [ReservationController::class, 'confirm'])->name('reservations.confirm');
        
        // AJAX routes for booking wizard
        Route::post('/available-rooms', [ReservationController::class, 'getAvailableRooms'])->name('reservations.available-rooms');
        Route::post('/hold-rooms', [ReservationController::class, 'holdRooms'])->name('reservations.hold-rooms');
        Route::post('/check-availability', [ReservationController::class, 'checkAvailability'])->name('reservations.check-availability');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    });
    
    // Rooms - Accessible to all authenticated users
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomsController::class, 'index'])->name('rooms');
        Route::get('/create', [RoomsController::class, 'create'])->name('rooms.create');
        Route::post('/', [RoomsController::class, 'store'])->name('rooms.store');
        Route::get('/{room}/edit', [RoomsController::class, 'edit'])->name('rooms.edit');
        Route::put('/{room}', [RoomsController::class, 'update'])->name('rooms.update');
        Route::delete('/{room}', [RoomsController::class, 'destroy'])->name('rooms.destroy');
        Route::get('/{room}', [RoomsController::class, 'show'])->name('rooms.show');
    });

    // =========================================================================
    // PAYMENT ROUTES - AUTHENTICATED USERS ONLY
    // =========================================================================
    Route::prefix('payments')->group(function () {
        // Payment details
        Route::get('/reservations/{reservation}/payment-details', [PaymentController::class, 'getPaymentDetails'])->name('payments.details');
        
        // Basic payment methods
        Route::post('/process-cash', [PaymentController::class, 'processCashPayment'])->name('payments.process-cash');
        Route::post('/process-card', [PaymentController::class, 'processCardPayment'])->name('payments.process-card');
        
        // Stripe Online Payment Routes
        Route::post('/process-online', [PaymentController::class, 'processOnlinePayment'])->name('payments.process-online');
        Route::get('/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('payments.stripe.success');
        Route::get('/stripe/cancel', [PaymentController::class, 'stripeCancel'])->name('payments.stripe.cancel');
        Route::get('/qrcode', [PaymentController::class, 'getPaymentQRCode'])->name('payments.qrcode');
        Route::get('/check-status', [PaymentController::class, 'checkPaymentStatus'])->name('payments.check-status');
        
        // Stripe Webhook (exclude CSRF protection)
        Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook'])->withoutMiddleware(['csrf']);
    });

    // =============================================================================
    // ADMIN-ONLY ROUTES - ADMIN CAN ACCESS EVERYTHING
    // =============================================================================
    Route::middleware(['auth', 'admin'])->group(function () {
        // Employee Management - Admin only
        Route::prefix('employee')->group(function () {
            Route::get('/', [UsersController::class, 'index'])->name('users.index');
            Route::post('/', [UsersController::class, 'store'])->name('users.store');
            Route::put('/{user}', [UsersController::class, 'update'])->name('users.update');
            Route::delete('/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
        });

        // Admin can also access all receptionist routes with admin prefix
        Route::prefix('admin')->group(function () {
            Route::get('/receptionist/dashboard', [DashboardController::class, 'index'])->name('admin.receptionist.dashboard');
            Route::get('/receptionist/reservations', [ReservationController::class, 'index'])->name('admin.receptionist.reservations');
            Route::get('/receptionist/rooms', [RoomsController::class, 'index'])->name('admin.receptionist.rooms');
            Route::get('/receptionist/guests', [TransactionController::class, 'index'])->name('admin.receptionist.guests');
            Route::get('/receptionist/reports', function () { 
                return view('reports'); 
            })->name('admin.receptionist.reports');
            Route::get('/receptionist/settings', function () { 
                return view('settings'); 
            })->name('admin.receptionist.settings');
        });
    });

    // =============================================================================
    // RECEPTIONIST-ONLY ROUTES
    // =============================================================================
    Route::middleware(['auth', 'receptionist'])->group(function () {
        // Receptionist Dashboard
        Route::get('/receptionist/dashboard', [DashboardController::class, 'index'])->name('receptionist.dashboard');

        // Receptionist Reservations
        Route::prefix('receptionist/reservations')->group(function () {
            Route::get('/', [ReservationController::class, 'index'])->name('receptionist.reservations');
            Route::get('/create', [ReservationController::class, 'create'])->name('receptionist.reservations.create');
            Route::post('/', [ReservationController::class, 'store'])->name('receptionist.reservations.store');
            Route::get('/{reservation}/edit', [ReservationController::class, 'edit'])->name('receptionist.reservations.edit');
            Route::put('/{reservation}', [ReservationController::class, 'update'])->name('receptionist.reservations.update');
            Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('receptionist.reservations.destroy');
            Route::get('/{reservation}', [ReservationController::class, 'show'])->name('receptionist.reservations.show');
            Route::post('/confirm/{reservation}', [ReservationController::class, 'confirm'])->name('receptionist.reservations.confirm');
        });

        // Receptionist Rooms
        Route::prefix('receptionist/rooms')->group(function () {
            Route::get('/', [RoomsController::class, 'index'])->name('receptionist.rooms');
            Route::get('/create', [RoomsController::class, 'create'])->name('receptionist.rooms.create');
            Route::post('/', [RoomsController::class, 'store'])->name('receptionist.rooms.store');
            Route::get('/{room}/edit', [RoomsController::class, 'edit'])->name('receptionist.rooms.edit');
            Route::put('/{room}', [RoomsController::class, 'update'])->name('receptionist.rooms.update');
            Route::delete('/{room}', [RoomsController::class, 'destroy'])->name('receptionist.rooms.destroy');
            Route::get('/{room}', [RoomsController::class, 'show'])->name('receptionist.rooms.show');
        });

        // Receptionist other pages
        Route::get('/receptionist/guests', [TransactionController::class, 'index'])->name('receptionist.guests');
        Route::get('/receptionist/reports', function () { return view('reports'); })->name('receptionist.reports');
        Route::get('/receptionist/settings', function () { return view('settings'); })->name('receptionist.settings');
    });

    // =============================================================================
    // DEBUG ROUTES (Temporary - Remove in production)
    // =============================================================================
    Route::get('/test-payment-route', function() {
        return response()->json([
            'success' => true,
            'message' => 'Route is working!'
        ]);
    });

    Route::get('/test-simple-json', function() {
        return response()->json([
            'success' => true,
            'message' => 'Simple JSON test works!',
            'data' => [
                'test' => 'value',
                'number' => 123
            ]
        ]);
    });

    Route::get('/debug-admin', function () {
        $user = auth()->user();
        
        if (!$user) {
            return "No user logged in";
        }
        
        return response()->json([
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
            'role_check' => $user->role === 'admin' ? 'PASS' : 'FAIL',
            'middleware' => 'Working if you see this message'
        ]);
    })->middleware(['auth', 'admin']);

    Route::get('/check-my-role', function () {
        $user = auth()->user();
        
        if (!$user) {
            return "No user logged in";
        }
        
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'is_active' => $user->is_active,
            'is_admin' => $user->role === 'admin',
            'session_data' => session()->all()
        ]);
    });
});

// =============================================================================
// FALLBACK ROUTE - 404 PAGE
// =============================================================================
Route::fallback(function () {
    return view('errors.404');
});