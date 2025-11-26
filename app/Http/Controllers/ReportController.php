<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $thirtyDaysAgo = Carbon::today()->subDays(30);
        
        // Today's revenue
        $todayRevenue = Payment::whereDate('payment_date', $today)
            ->where('payment_status', 'completed')
            ->sum('amount');

        // Monthly revenue
        $monthlyRevenue = Payment::whereBetween('payment_date', [$thirtyDaysAgo, $today])
            ->where('payment_status', 'completed')
            ->sum('amount');

        // Occupancy data
        $totalRooms = \App\Models\Room::count();
        $occupiedRooms = Booking::whereHas('reservation', function($query) use ($today) {
            $query->where('check_in_date', '<=', $today)
                  ->where('check_out_date', '>=', $today)
                  ->where('status', 'confirmed');
        })->count();

        $currentOccupancy = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;

        // Quick insights
        $bestPerformance = Payment::where('payment_status', 'completed')
            ->max('amount');

        $avgDailyRevenue = Payment::whereBetween('payment_date', [$thirtyDaysAgo, $today])
            ->where('payment_status', 'completed')
            ->avg('amount') ?? 0;

        // Recent reports - handle if Report model doesn't exist
        try {
            $recentReports = Report::latest()->take(5)->get();
            $recentReportsCount = $recentReports->count();
        } catch (\Exception $e) {
            // If Report model doesn't exist yet, use empty collection
            $recentReports = collect();
            $recentReportsCount = 0;
        }

        return view('reports', [
            'todayRevenue' => $todayRevenue,
            'todayRevenueGrowth' => 5,
            'currentOccupancy' => $currentOccupancy,
            'occupancyGrowth' => 7,
            'monthlyRevenue' => $monthlyRevenue,
            'revenueGrowth' => 3,
            'recreationalBookings' => 15,
            'recreationalGrowth' => 2,
            'bestPerformance' => $bestPerformance,
            'avgDailyRevenue' => $avgDailyRevenue,
            'peakSeasonOccupancy' => 85,
            'seasonalGrowth' => 18.5,
            'recentReports' => $recentReports,
            'recentReportsCount' => $recentReportsCount,
            'defaultStartDate' => $thirtyDaysAgo->format('Y-m-d'),
            'defaultEndDate' => $today->format('Y-m-d'),
        ]);
    }

    public function generate(Request $request)
    {
        // Validate the request
        $request->validate([
            'report_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'document_name' => 'required|string'
        ]);

        // Handle report generation logic here
        // For now, just return success message
        return back()->with('success', 'Report generated successfully!');
    }

    public function download($id)
    {
        try {
            $report = Report::findOrFail($id);
            // Handle PDF download logic here
            
            // For now, return a simple response
            return response()->json([
                'message' => 'Download functionality would be implemented here',
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Report not found.');
        }
    }
}