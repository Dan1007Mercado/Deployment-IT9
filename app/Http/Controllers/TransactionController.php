<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use App\Models\Reservation;
use App\Models\Guest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $status = $request->get('status');
        $method = $request->get('method');
        $search = $request->get('search');
        
        // Base query with relationships
        $query = Payment::with([
            'booking.reservation.guest',
            'booking.reservation.roomType',
            'booking.rooms.room'
        ]);
        
        // Apply filters
        if ($status) {
            $query->where('payment_status', $status);
        }
        
        if ($method) {
            $query->where('payment_method', $method);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('booking.reservation.guest', function($guestQuery) use ($search) {
                    $guestQuery->where('first_name', 'like', "%{$search}%")
                              ->orWhere('last_name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('transaction_id', 'like', "%{$search}%")
                ->orWhereHas('booking.rooms.room', function($roomQuery) use ($search) {
                    $roomQuery->where('room_number', 'like', "%{$search}%");
                });
            });
        }
        
        // Get paginated results
        $transactions = $query->orderBy('payment_date', 'desc')
                            ->paginate(10)
                            ->appends($request->query());
        
        // Calculate summary statistics
        $totalRevenue = Payment::where('payment_status', 'completed')->sum('amount');
        
        // Calculate revenue growth (simplified for demo)
        $lastMonthRevenue = Payment::where('payment_status', 'completed')
                                ->where('payment_date', '>=', Carbon::now()->subMonth())
                                ->sum('amount');
        
        $previousMonthRevenue = Payment::where('payment_status', 'completed')
                                    ->whereBetween('payment_date', [
                                        Carbon::now()->subMonths(2)->startOfMonth(),
                                        Carbon::now()->subMonths(2)->endOfMonth()
                                    ])
                                    ->sum('amount');
        
        $revenueGrowth = $previousMonthRevenue > 0 
            ? round((($lastMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100, 1)
            : 0;
        
        $pendingAmount = Payment::where('payment_status', 'pending')->sum('amount');
        $pendingCount = Payment::where('payment_status', 'pending')->count();
        
        $todayRevenue = Payment::where('payment_status', 'completed')
                            ->whereDate('payment_date', Carbon::today())
                            ->sum('amount');
        
        $todayCount = Payment::where('payment_status', 'completed')
                            ->whereDate('payment_date', Carbon::today())
                            ->count();
        
        $failedAmount = Payment::whereIn('payment_status', ['failed', 'refunded'])->sum('amount');
        $failedCount = Payment::whereIn('payment_status', ['failed', 'refunded'])->count();
        
        return view('guests', compact(
            'transactions',
            'totalRevenue',
            'revenueGrowth',
            'pendingAmount',
            'pendingCount',
            'todayRevenue',
            'todayCount',
            'failedAmount',
            'failedCount'
        ));
    }
}