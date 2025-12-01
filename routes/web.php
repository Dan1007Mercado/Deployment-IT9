<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\RoomsController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;

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
// GMAIL OAUTH CALLBACK ROUTE (PUBLIC - No auth required)
// =============================================================================
Route::get('/oauth2callback', function (Request $request) {
    try {
        $code = $request->get('code');
        
        if (!$code) {
            Log::error('Gmail OAuth callback received without authorization code');
            return redirect('/settings')->with('error', 'Authentication failed: No authorization code received');
        }

        $gmailService = new \App\Services\GmailService();
        $gmailService->setAuthCode($code);
        
        Log::info('Gmail OAuth authentication completed successfully');
        return redirect('/settings')->with('success', 'Gmail API successfully authenticated! You can now send emails.');
        
    } catch (\Exception $e) {
        Log::error('Gmail OAuth callback error: ' . $e->getMessage());
        return redirect('/settings')->with('error', 'Authentication failed: ' . $e->getMessage());
    }
})->name('gmail.callback');

// =============================================================================
// PROTECTED ROUTES - AUTHENTICATED USERS ONLY
// =============================================================================
Route::middleware('auth')->group(function () {
    
    // Dashboard Routes - Accessible to all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Transactions/Guests Routes - Dynamic with controller
    Route::get('/guests', [TransactionController::class, 'index'])->name('guests');
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    
    // =========================================================================
    // REPORTS ROUTES - DYNAMIC WITH CONTROLLER
    // =========================================================================
    Route::prefix('reports')->group(function () {
        // Main reports page
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        
        // Generate report
        Route::post('/generate', [ReportController::class, 'generate'])->name('reports.generate');
        
        // Download report as PDF
        Route::get('/download/{id}', [ReportController::class, 'download'])->name('reports.download');
        
        // View report inline in browser
        Route::get('/view/{id}', [ReportController::class, 'view'])->name('reports.view');
        
        // Delete report
        Route::delete('/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
        
        // Quick report generation (if needed)
        Route::get('/quick/{type}', [ReportController::class, 'quickReport'])->name('reports.quick');
    });
    
    // Legacy report routes (for compatibility)
    Route::get('/report', [ReportController::class, 'index'])->name('report');
    Route::post('/report/generate', [ReportController::class, 'generate'])->name('report.generate');
    Route::get('/report/download/{id}', [ReportController::class, 'download'])->name('report.download');
    Route::get('/report/view/{id}', [ReportController::class, 'view'])->name('report.view');
    
    // Settings - Static page
    Route::get('/settings', function () { 
        return view('settings'); 
    })->name('settings');
    
    // =============================================================================
    // RESERVATIONS ROUTES - AUTHENTICATED USERS ONLY
    // =============================================================================
    Route::prefix('reservations')->group(function () {
        // IMAGE ROUTE - ADD THIS
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
        
        // AJAX routes for booking wizard
        Route::post('/available-rooms', [ReservationController::class, 'getAvailableRooms'])->name('reservations.available-rooms');
        Route::post('/hold-rooms', [ReservationController::class, 'holdRooms'])->name('reservations.hold-rooms');
        Route::post('/check-availability', [ReservationController::class, 'checkAvailability'])->name('reservations.check-availability');
        
        // EMAIL VALIDATION ROUTE - ADDED HERE
        Route::post('/check-email', [ReservationController::class, 'checkEmail'])->name('reservations.check-email');
        
        // Simple bookings route that redirects to reservations
        Route::get('/bookings', function (Request $request) {
            $checkInDate = $request->get('check_in_date', today()->format('Y-m-d'));
            return redirect()->route('reservations.index', ['check_in_date' => $checkInDate]);
        })->name('bookings.index');
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
            Route::get('/receptionist/report', [ReportController::class, 'index'])->name('admin.receptionist.report');
            Route::get('/receptionist/reports', [ReportController::class, 'index'])->name('admin.receptionist.reports');
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
            // IMAGE ROUTE FOR RECEPTIONIST TOO
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
            
            // AJAX routes for receptionist booking wizard
            Route::post('/get-available-rooms', [ReservationController::class, 'getAvailableRooms'])->name('receptionist.reservations.get-available-rooms');
            Route::post('/hold-rooms', [ReservationController::class, 'holdRooms'])->name('receptionist.reservations.hold-rooms');
            Route::post('/check-availability', [ReservationController::class, 'checkAvailability'])->name('receptionist.reservations.check-availability');
            
            // EMAIL VALIDATION ROUTE FOR RECEPTIONIST - ADDED HERE
            Route::post('/check-email', [ReservationController::class, 'checkEmail'])->name('receptionist.reservations.check-email');
            
            // Simple bookings route for receptionist
            Route::get('/bookings', function (Request $request) {
                $checkInDate = $request->get('check_in_date', today()->format('Y-m-d'));
                return redirect()->route('receptionist.reservations', ['check_in_date' => $checkInDate]);
            })->name('receptionist.bookings.index');
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
    
    Route::get('/test-pdf-generation', function() {
        try {
            $pdf = Pdf::loadView('reports.pdf', [
                'reportType' => 'revenue',
                'reportName' => 'Test Revenue Report',
                'startDate' => now()->subDays(30),
                'endDate' => now(),
                'reportData' => [
                    'totalRevenue' => 50000,
                    'roomRevenue' => 45000,
                    'otherRevenue' => 5000,
                    'totalBookings' => 25,
                    'revenueGrowth' => 12.5
                ],
                'logoPath' => 'images/logo.png'
            ]);
            
            return $pdf->stream('test-report.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->name('test.pdf');

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
            'is_receptionist' => $user->role === 'receptionist',
            'session_data' => session()->all()
        ]);
    });
    
    // Test report routes
    Route::get('/test/report-data/{type}', function($type) {
        $controller = new ReportController();
        $startDate = now()->subDays(30);
        $endDate = now();
        
        try {
            $data = $controller->generateReportData($type, $startDate, $endDate);
            
            return response()->json([
                'success' => true,
                'report_type' => $type,
                'data' => $data,
                'date_range' => $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    });
});

// =============================================================================
// PUBLIC REPORT ROUTES (for shared reports via link)
// =============================================================================
Route::prefix('public')->group(function () {
    Route::get('/report/{id}/{token}', [ReportController::class, 'publicView'])
        ->name('reports.public.view')
        ->middleware('throttle:60,1');
    
    Route::get('/report/download/{id}/{token}', [ReportController::class, 'publicDownload'])
        ->name('reports.public.download')
        ->middleware('throttle:30,1');
});

// =============================================================================
// API ROUTES FOR AJAX REPORT GENERATION
// =============================================================================
Route::prefix('api')->middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::get('/report/preview', [ReportController::class, 'previewReport'])->name('api.report.preview');
    Route::post('/report/generate-async', [ReportController::class, 'generateAsync'])->name('api.report.generate-async');
    Route::get('/report/status/{jobId}', [ReportController::class, 'checkGenerationStatus'])->name('api.report.status');
    Route::get('/reports/list', [ReportController::class, 'listReports'])->name('api.reports.list');
});

// =============================================================================
// FALLBACK ROUTE - 404 PAGE
// =============================================================================
Route::fallback(function () {
    return view('errors.404');
});