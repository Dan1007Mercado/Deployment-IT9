<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestCheckController extends Controller
{
    public function index(Request $request)
    {
        $today = $request->get('date', Carbon::today()->toDateString());
        $search = $request->get('search');
        
        // Get today's check-ins with pagination
        $checkInsQuery = Booking::with(['reservation.guest', 'rooms.room'])
            ->whereHas('reservation', function($query) use ($today, $search) {
                $query->where('check_in_date', $today)
                      ->whereIn('status', ['confirmed', 'pending']);
                
                // Add search functionality
                if ($search) {
                    $query->whereHas('guest', function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('reservation_id', 'like', "%{$search}%");
                }
            })
            ->where('booking_status', 'reserved');

        $checkIns = $checkInsQuery->paginate(10);

        // Get today's check-outs with pagination
        $checkOutsQuery = Booking::with(['reservation.guest', 'rooms.room'])
            ->whereHas('reservation', function($query) use ($today, $search) {
                $query->where('check_out_date', $today)
                      ->where('status', 'confirmed');
                
                // Add search functionality
                if ($search) {
                    $query->whereHas('guest', function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('reservation_id', 'like', "%{$search}%");
                }
            })
            ->where('booking_status', 'checked-in');

        $checkOuts = $checkOutsQuery->paginate(10);

        // Get currently checked-in guests with pagination
        $currentGuestsQuery = Booking::with(['reservation.guest', 'rooms.room'])
            ->where('booking_status', 'checked-in')
            ->whereHas('reservation', function($query) use ($search) {
                $query->where('status', 'confirmed');
                
                // Add search functionality
                if ($search) {
                    $query->whereHas('guest', function($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhere('reservation_id', 'like', "%{$search}%");
                }
            });

        $currentGuests = $currentGuestsQuery->paginate(10);

        return view('guest-check.index', compact(
            'checkIns',
            'checkOuts', 
            'currentGuests',
            'today',
            'search'
        ));
    }

    public function checkIn(Booking $booking)
    {
        DB::beginTransaction();
        try {
            $booking->update([
                'booking_status' => 'checked-in',
                'actual_check_in' => now()
            ]);

            // Update room status
            foreach ($booking->rooms as $bookingRoom) {
                $bookingRoom->room->update([
                    'room_status' => 'occupied'
                ]);
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Guest checked in successfully!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to check in: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkOut(Booking $booking)
    {
        DB::beginTransaction();
        try {
            $booking->update([
                'booking_status' => 'checked-out',
                'actual_check_out' => now()
            ]);

            // Update room status
            foreach ($booking->rooms as $bookingRoom) {
                $bookingRoom->room->update([
                    'room_status' => 'available'
                ]);
            }

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Guest checked out successfully!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to check out: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function home()
    {
        $roomTypes = RoomType::with(['rooms' => function($query) {
            $query->where('room_status', 'available');
        }])->get();
        
        return view('home', compact('roomTypes'));
    }

    public function confirmBooking(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'contact_number' => 'required|string|max:20',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'num_guests' => 'required|integer|min:1',
            'room_ids' => 'required|array|min:1',
            'room_ids.*' => 'exists:rooms,room_id',
            'total_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,online'
        ]);

        // Add your existing reservation creation logic here
        // This should be similar to ReservationController@store
        
        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully',
            'payment_url' => null, // Set this for online payments
            'reservation_id' => 123 // Return the created reservation ID
        ]);
    }
    public function quickCheckIn(Booking $booking)
    {
        return $this->checkIn($booking);
    }

    public function quickCheckOut(Booking $booking)
    {
        return $this->checkOut($booking);
    }
}