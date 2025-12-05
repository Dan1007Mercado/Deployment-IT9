<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GuestBookingController;
use App\Http\Controllers\GuestCheckController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// =========================================================================
// PUBLIC ROUTES (No Authentication Required)
// =========================================================================

// Default route (login page)
Route::get('/', function () {
    return view('login');
})->name('home');

// Home page with room types
Route::get('/home', [GuestBookingController::class, 'home'])->name('home.page');

// =========================================================================
// STRIPE PAYMENT CALLBACK ROUTES (PUBLIC - Accessed by Stripe)
// =========================================================================
Route::prefix('payments')->group(function () {
    // QR Code Display (Public - no auth)
    Route::get('/qr-code-display', function(Request $request) {
        return view('components.payments.qr-code-modal', [
            'payment_url' => $request->get('payment_url'),
            'session_id' => $request->get('session_id'),
            'reservation_id' => $request->get('reservation_id'),
            'amount' => $request->get('amount')
        ]);
    })->name('payments.qr-code-display');
    
    // Stripe success callback
    Route::get('/stripe/success', [PaymentController::class, 'stripeSuccess'])->name('payments.stripe.success');
    
    // Stripe cancel callback
    Route::get('/stripe/cancel', [PaymentController::class, 'stripeCancel'])->name('payments.stripe.cancel');
});
Route::get('/oauth2callback', function(Request $request) {
    $code = $request->query('code');

    if (!$code) {
        return 'No authorization code received';
    }

    $client = new Google_Client();
    $client->setAuthConfig(storage_path('app/credentials.json'));
    $client->setRedirectUri('http://localhost:8000/oauth2callback'); // must match Google Cloud
    $client->setAccessType('offline');
    $client->setScopes([Google_Service_Gmail::GMAIL_SEND]);

    // Exchange code for token
    $accessToken = $client->fetchAccessTokenWithAuthCode($code);

    // Save token for future use
    $tokenPath = storage_path('app/gmail-token.json');
    if (!file_exists(dirname($tokenPath))) {
        mkdir(dirname($tokenPath), 0700, true);
    }
    file_put_contents($tokenPath, json_encode($accessToken));

    return 'Gmail authorization successful! Token saved.';
});
// Stripe Webhook (Public - called by Stripe servers)
Route::post('/stripe/webhook', [PaymentController::class, 'handleWebhook'])->withoutMiddleware(['csrf']);

// =========================================================================
// HOTEL WEBSITE & GUEST BOOKING ROUTES (Public)
// =========================================================================

// Hotel main website
Route::get('/hotel', [GuestBookingController::class, 'home'])->name('guest.home');

// Guest booking process - AJAX routes
Route::prefix('hotel')->group(function () {
    // Check availability
    Route::post('/check-availability', [GuestBookingController::class, 'checkAvailability'])->name('guest.check-availability');
    
    // Room details
    Route::get('/room/{roomType}', [GuestBookingController::class, 'roomDetails'])->name('guest.room.details');
    
    // Booking process
    Route::post('/booking/prepare', [GuestBookingController::class, 'prepareBooking'])->name('guest.booking.prepare');
    Route::post('/booking/confirm', [GuestBookingController::class, 'confirmBooking'])->name('guest.booking.confirm');
    
    // Online payment
    Route::post('/booking/payment/online', [GuestBookingController::class, 'processOnlinePayment'])->name('guest.booking.payment.online');
    
    // Booking results
    Route::get('/booking/success/{reservation}', [GuestBookingController::class, 'bookingSuccess'])->name('guest.booking.success');
    Route::get('/booking/cancel/{reservation}', [GuestBookingController::class, 'bookingCancel'])->name('guest.booking.cancel');
});

// =========================================================================
// AUTHENTICATION ROUTES
// =========================================================================

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =========================================================================
// PROTECTED ROUTES - AUTHENTICATED USERS ONLY
// =========================================================================
Route::middleware('auth')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Transactions/Guests
    Route::get('/guests', [TransactionController::class, 'index'])->name('guests');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    
    // =====================================================================
    // GUEST CHECK-IN/OUT ROUTES - ADDED HERE
    // =====================================================================
    Route::prefix('guest-check')->group(function () {
        Route::get('/', [GuestCheckController::class, 'index'])->name('guest-check.index');
        Route::post('/check-in/{booking}', [GuestCheckController::class, 'checkIn'])->name('guest-check.checkin');
        Route::post('/check-out/{booking}', [GuestCheckController::class, 'checkOut'])->name('guest-check.checkout');
        Route::post('/quick-checkin/{booking}', [GuestCheckController::class, 'quickCheckIn'])->name('guest-check.quick-checkin');
        Route::post('/quick-checkout/{booking}', [GuestCheckController::class, 'quickCheckOut'])->name('guest-check.quick-checkout');
    });

    // Legacy route for sidebar compatibility
    Route::get('/Guest-Checkin', [GuestCheckController::class, 'index'])->name('checkin');
    
    // =====================================================================
    // REPORTS ROUTES
    // =====================================================================
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('/download/{id}', [ReportController::class, 'download'])->name('reports.download');
        Route::get('/view/{id}', [ReportController::class, 'view'])->name('reports.view');
        Route::delete('/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
        Route::get('/quick/{type}', [ReportController::class, 'quickReport'])->name('reports.quick');
    });
    
    // Legacy report routes
    Route::get('/report', [ReportController::class, 'index'])->name('report');
    Route::post('/report/generate', [ReportController::class, 'generate'])->name('report.generate');
    Route::get('/report/download/{id}', [ReportController::class, 'download'])->name('report.download');
    Route::get('/report/view/{id}', [ReportController::class, 'view'])->name('report.view');
    
    // =====================================================================
    // RESERVATIONS ROUTES
    // =====================================================================
    Route::prefix('reservations')->group(function () {
        // Image route
        Route::get('/room-images/{filename}', function ($filename) {
            $path = storage_path('app/public/room-images/' . $filename);
            
            if (!file_exists($path)) {
                abort(404);
            }
            
            return response()->file($path);
        })->name('reservations.room-images');
        
        // Main reservation routes
        Route::get('/', [ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/create', [ReservationController::class, 'create'])->name('reservations.create');
        Route::post('/', [ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
        Route::get('/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
        Route::put('/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
        Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
        Route::post('/confirm/{reservation}', [ReservationController::class, 'confirm'])->name('reservations.confirm');
        
        // Payment details
        Route::get('/{reservation}/payment-details', [ReservationController::class, 'getPaymentDetails'])->name('reservations.payment-details');
        
        // AJAX routes
        Route::post('/available-rooms', [ReservationController::class, 'getAvailableRooms'])->name('reservations.available-rooms');
        Route::post('/hold-rooms', [ReservationController::class, 'holdRooms'])->name('reservations.hold-rooms');
        Route::post('/check-availability', [ReservationController::class, 'checkAvailability'])->name('reservations.check-availability');
        Route::post('/check-email', [ReservationController::class, 'checkEmail'])->name('reservations.check-email');
        
        // Simple bookings redirect
        Route::get('/bookings', function (Request $request) {
            $checkInDate = $request->get('check_in_date', today()->format('Y-m-d'));
            return redirect()->route('reservations.index', ['check_in_date' => $checkInDate]);
        })->name('bookings.index');
    });
    
    // =====================================================================
    // ROOMS ROUTES
    // =====================================================================
    Route::prefix('rooms')->group(function () {
        Route::get('/', [RoomsController::class, 'index'])->name('rooms');
        Route::post('/', [RoomsController::class, 'store'])->name('rooms.store');
        Route::put('/{room}', [RoomsController::class, 'update'])->name('rooms.update');
        Route::delete('/{room}', [RoomsController::class, 'destroy'])->name('rooms.destroy');
    });

    // =====================================================================
    // PAYMENT PROCESSING ROUTES (Protected - Staff Only)
    // =====================================================================
    Route::prefix('payments')->group(function () {
        // Payment details
        Route::get('/reservations/{reservation}/payment-details', [PaymentController::class, 'getPaymentDetails'])->name('payments.details');
        
        // Basic payment methods
        Route::post('/process-cash', [PaymentController::class, 'processCashPayment'])->name('payments.process-cash');
        Route::post('/process-card', [PaymentController::class, 'processCardPayment'])->name('payments.process-card');
        
        // Stripe Online Payment Initiation
        Route::post('/process-online', [PaymentController::class, 'processOnlinePayment'])->name('payments.process-online');
        
        // Get existing payment QR code
        Route::get('/qrcode', [PaymentController::class, 'getPaymentQRCode'])->name('payments.qrcode');
        
        // Check payment status
        Route::get('/check-status', [PaymentController::class, 'checkPaymentStatus'])->name('payments.check-status');
    });

    // =====================================================================
    // ADMIN-ONLY ROUTES
    // =====================================================================
    Route::middleware(['auth', 'admin'])->group(function () {
        // Employee Management
        Route::prefix('employee')->group(function () {
            Route::get('/', [UsersController::class, 'index'])->name('users.index');
            Route::post('/', [UsersController::class, 'store'])->name('users.store');
            Route::put('/{user}', [UsersController::class, 'update'])->name('users.update');
            Route::delete('/{user}', [UsersController::class, 'destroy'])->name('users.destroy');
        });

        // Admin access to all receptionist routes
        Route::prefix('admin')->group(function () {
            Route::get('/receptionist/dashboard', [DashboardController::class, 'index'])->name('admin.receptionist.dashboard');
            Route::get('/receptionist/reservations', [ReservationController::class, 'index'])->name('admin.receptionist.reservations');
            Route::get('/receptionist/rooms', [RoomsController::class, 'index'])->name('admin.receptionist.rooms');
            Route::get('/receptionist/guests', [TransactionController::class, 'index'])->name('admin.receptionist.guests');
            Route::get('/receptionist/report', [ReportController::class, 'index'])->name('admin.receptionist.report');
            Route::get('/receptionist/guest-check', [GuestCheckController::class, 'index'])->name('admin.receptionist.guest-check');
        });
    });

    // =====================================================================
    // RECEPTIONIST-ONLY ROUTES
    // =====================================================================
    Route::middleware(['auth', 'receptionist'])->group(function () {
        // Receptionist Dashboard
        Route::get('/receptionist/dashboard', [DashboardController::class, 'index'])->name('receptionist.dashboard');

        // Receptionist Reservations
        Route::prefix('receptionist/reservations')->group(function () {
            // Image route for receptionist
            Route::get('/room-images/{filename}', function ($filename) {
                $path = storage_path('app/public/room-images/' . $filename);
                
                if (!file_exists($path)) {
                    abort(404);
                }
                
                return response()->file($path);
            })->name('receptionist.reservations.room-images');
            
            // Main reservation routes for receptionist
            Route::get('/', [ReservationController::class, 'index'])->name('receptionist.reservations');
            Route::get('/create', [ReservationController::class, 'create'])->name('receptionist.reservations.create');
            Route::post('/', [ReservationController::class, 'store'])->name('receptionist.reservations.store');
            Route::get('/{reservation}', [ReservationController::class, 'show'])->name('receptionist.reservations.show');
            Route::get('/{reservation}/edit', [ReservationController::class, 'edit'])->name('receptionist.reservations.edit');
            Route::put('/{reservation}', [ReservationController::class, 'update'])->name('receptionist.reservations.update');
            Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('receptionist.reservations.destroy');
            Route::post('/confirm/{reservation}', [ReservationController::class, 'confirm'])->name('receptionist.reservations.confirm');
            
            // Payment details for receptionist
            Route::get('/{reservation}/payment-details', [ReservationController::class, 'getPaymentDetails'])->name('receptionist.reservations.payment-details');
            
            // AJAX routes for receptionist
            Route::post('/get-available-rooms', [ReservationController::class, 'getAvailableRooms'])->name('receptionist.reservations.get-available-rooms');
            Route::post('/hold-rooms', [ReservationController::class, 'holdRooms'])->name('receptionist.reservations.hold-rooms');
            Route::post('/check-availability', [ReservationController::class, 'checkAvailability'])->name('receptionist.reservations.check-availability');
            Route::post('/check-email', [ReservationController::class, 'checkEmail'])->name('receptionist.reservations.check-email');
        });

        // Receptionist Rooms
        Route::prefix('receptionist/rooms')->group(function () {
            Route::get('/', [RoomsController::class, 'index'])->name('receptionist.rooms');
            Route::post('/', [RoomsController::class, 'store'])->name('receptionist.rooms.store');
            Route::put('/{room}', [RoomsController::class, 'update'])->name('receptionist.rooms.update');
            Route::delete('/{room}', [RoomsController::class, 'destroy'])->name('receptionist.rooms.destroy');
        });

        // Receptionist Guest Check-in/Out
        Route::prefix('receptionist/guest-check')->group(function () {
            Route::get('/', [GuestCheckController::class, 'index'])->name('receptionist.guest-check');
            Route::post('/check-in/{booking}', [GuestCheckController::class, 'checkIn'])->name('receptionist.guest-check.checkin');
            Route::post('/check-out/{booking}', [GuestCheckController::class, 'checkOut'])->name('receptionist.guest-check.checkout');
            Route::post('/quick-checkin/{booking}', [GuestCheckController::class, 'quickCheckIn'])->name('receptionist.guest-check.quick-checkin');
            Route::post('/quick-checkout/{booking}', [GuestCheckController::class, 'quickCheckOut'])->name('receptionist.guest-check.quick-checkout');
        });

        // Receptionist Reports
        Route::prefix('receptionist/reports')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('receptionist.reports');
            Route::post('/generate', [ReportController::class, 'generate'])->name('receptionist.reports.generate');
            Route::get('/download/{id}', [ReportController::class, 'download'])->name('receptionist.reports.download');
            Route::get('/view/{id}', [ReportController::class, 'view'])->name('receptionist.reports.view');
        });

        // Receptionist other pages
        Route::get('/receptionist/guests', [TransactionController::class, 'index'])->name('receptionist.guests');
        Route::get('/receptionist/report', [ReportController::class, 'index'])->name('receptionist.report');
        Route::get('/receptionist/Guest-Checkin', [GuestCheckController::class, 'index'])->name('receptionist.checkin');
    });

    // =====================================================================
    // TEST/DEBUG ROUTES
    // =====================================================================
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
            'is_receptionist' => $user->role === 'receptionist'
        ]);
    });
});

// =========================================================================
// PUBLIC GUEST BOOKING ROUTES
// =========================================================================

// Add these AJAX routes for the home page booking modal
Route::prefix('booking')->group(function () {
    // Available rooms AJAX
    Route::post('/available-rooms', [ReservationController::class, 'getAvailableRooms'])->name('guest.booking.available-rooms');
    
    // Check email availability
    Route::post('/check-email', [ReservationController::class, 'checkEmail'])->name('guest.booking.check-email');
    
    // Confirm booking (from modal)
    Route::post('/confirm', [GuestBookingController::class, 'confirmBooking'])->name('guest.booking.confirm');
    
    // Process payment (from modal)
    Route::post('/process-payment', [GuestBookingController::class, 'processPayment'])->name('guest.booking.process-payment');
});

// =========================================================================
// PUBLIC REPORT ROUTES (for shared reports via link)
// =========================================================================
Route::prefix('public')->group(function () {
    Route::get('/report/{id}/{token}', [ReportController::class, 'publicView'])
        ->name('reports.public.view')
        ->middleware('throttle:60,1');
    
    Route::get('/report/download/{id}/{token}', [ReportController::class, 'publicDownload'])
        ->name('reports.public.download')
        ->middleware('throttle:30,1');
});

// =========================================================================
// API ROUTES FOR AJAX REPORT GENERATION
// =========================================================================
Route::prefix('api')->middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('/report/preview', [ReportController::class, 'previewReport'])->name('api.report.preview');
    Route::post('/report/generate-async', [ReportController::class, 'generateAsync'])->name('api.report.generate-async');
    Route::get('/report/status/{jobId}', [ReportController::class, 'checkGenerationStatus'])->name('api.report.status');
    Route::get('/reports/list', [ReportController::class, 'listReports'])->name('api.reports.list');
});

// =========================================================================
// FALLBACK ROUTE - 404 PAGE
// =========================================================================
Route::fallback(function () {
    return view('errors.404');
});