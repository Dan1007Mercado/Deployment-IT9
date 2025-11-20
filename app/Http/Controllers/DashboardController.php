<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Payment;
use App\Models\Guest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get date filters with defaults
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Basic stats
        $stats = [
            'available_rooms' => Room::where('room_status', 'available')->count(),
            'occupied_rooms' => Room::where('room_status', 'occupied')->count(),
            'today_checkins' => Reservation::whereDate('check_in_date', today())->count(),
            'today_checkouts' => Reservation::whereDate('check_out_date', today())->count(),
            'total_guests' => Guest::count(),
            'total_reservations' => Reservation::count(),
        ];

        // Revenue data
        $revenueData = $this->getRevenueData($startDate, $endDate);
        
        // Recent reservations for table
        $recentReservations = Reservation::with(['guest', 'roomType'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboard', array_merge($stats, $revenueData, [
            'recent_reservations' => $recentReservations,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]));
    }

    private function getRevenueData($startDate, $endDate)
    {
        // Total revenue for date range
        $totalRevenue = Payment::where('payment_status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        // Monthly revenue for chart (last 6 months)
        $monthlyRevenue = [];
        $chartLabels = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $revenue = Payment::where('payment_status', 'completed')
                ->whereBetween('payment_date', [$monthStart, $monthEnd])
                ->sum('amount');
                
            $monthlyRevenue[] = $revenue;
            $chartLabels[] = $month->format('M Y');
        }

        return [
            'total_revenue' => $totalRevenue,
            'monthly_revenue_data' => $monthlyRevenue,
            'revenue_chart_labels' => $chartLabels,
        ];
    }
}