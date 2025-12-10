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
        
        // Get today's check-ins (guests scheduled to check-in today)
        $checkInsQuery = Booking::with(['reservation.guest', 'rooms.room'])
            ->whereHas('reservation', function($query) use ($today, $search) {
                $query->where('check_in_date', $today)
                      ->whereIn('status', ['confirmed', 'pending']);
                
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->whereHas('guest', function($guestQuery) use ($search) {
                            $guestQuery->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhere('reservation_id', 'like', "%{$search}%");
                    });
                }
            })
            ->where('booking_status', 'reserved'); // Not checked in yet

        $checkIns = $checkInsQuery->paginate(10);

        // Get today's check-outs (guests who ACTUALLY checked out today)
        $checkOutsQuery = Booking::with(['reservation.guest', 'rooms.room'])
            ->where('booking_status', 'checked-out')
            ->whereDate('actual_check_out', $today)
            ->whereHas('reservation', function($query) use ($search) {
                $query->where('status', 'confirmed');
                
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->whereHas('guest', function($guestQuery) use ($search) {
                            $guestQuery->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhere('reservation_id', 'like', "%{$search}%");
                    });
                }
            });

        $checkOuts = $checkOutsQuery->paginate(10);

        // Get currently checked-in guests
        $currentGuestsQuery = Booking::with(['reservation.guest', 'rooms.room'])
            ->where('booking_status', 'checked-in')
            ->whereHas('reservation', function($query) use ($search) {
                $query->where('status', 'confirmed');
                
                if ($search) {
                    $query->where(function($q) use ($search) {
                        $q->whereHas('guest', function($guestQuery) use ($search) {
                            $guestQuery->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhere('reservation_id', 'like', "%{$search}%");
                    });
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

    public function quickCheckIn(Booking $booking)
    {
        return $this->checkIn($booking);
    }

    public function quickCheckOut(Booking $booking)
    {
        return $this->checkOut($booking);
    }
}