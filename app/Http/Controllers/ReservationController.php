<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\RoomHold;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    // In your ReservationController index method
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $search = $request->get('search');
        
        $query = Reservation::with(['guest', 'roomType', 'bookings.rooms.room']);
        
        if ($search) {
            $query->whereHas('guest', function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('roomType', function($q) use ($search) {
                $q->where('type_name', 'like', "%{$search}%");
            });
        }
        
        $reservations = $query->latest()->paginate($perPage);
        
        return view('reservations', compact('reservations'));
    }

    // Show booking wizard
    public function create()
    {
        $roomTypes = RoomType::all();
        return view('reservations.booking-wizard', compact('roomTypes'));
    }

    // Get available rooms for selected dates and room type
    // Add this method to your ReservationController
    public function getAvailableRooms(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date',
            'room_type_id' => 'required|exists:room_types,room_type_id'
        ]);

        try {
            $availableRooms = Room::getAvailableRooms(
                $request->check_in_date,
                $request->check_out_date,
                $request->room_type_id
            );

            return response()->json([
                'success' => true,
                'rooms' => $availableRooms->map(function($room) {
                    return [
                        'room_id' => $room->room_id,
                        'room_number' => $room->room_number,
                        'floor' => $room->floor,
                        'room_type' => $room->roomType->type_name,
                        'price' => $room->roomType->base_price,
                        'image_path' => $room->image_path
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load available rooms: ' . $e->getMessage()
            ], 500);
        }
    }

    // Hold rooms temporarily during selection
    public function holdRooms(Request $request)
    {
        $request->validate([
            'room_ids' => 'required|array',
            'room_ids.*' => 'exists:rooms,room_id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date'
        ]);

        $sessionId = session()->getId();
        $expiresAt = now()->addMinutes(15); // Hold for 15 minutes

        DB::beginTransaction();
        try {
            // Remove existing holds for this session
            RoomHold::where('session_id', $sessionId)->delete();

            // Create new holds
            foreach ($request->room_ids as $roomId) {
                RoomHold::create([
                    'room_id' => $roomId,
                    'session_id' => $sessionId,
                    'check_in_date' => $request->check_in_date,
                    'check_out_date' => $request->check_out_date,
                    'expires_at' => $expiresAt
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Rooms held successfully',
                'expires_at' => $expiresAt
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to hold rooms'
            ], 500);
        }
    }

    // Create reservation with multiple rooms
    public function store(Request $request)
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
        'booking_source' => 'required|in:walk-in,phone,online,agent',
        'special_requests' => 'nullable|string',
        'total_amount' => 'required|numeric|min:0'
    ]);

    DB::beginTransaction();
    try {
        // Create or find guest
        $guest = Guest::firstOrCreate(
            ['email' => $request->email],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'contact_number' => $request->contact_number,
                'guest_type' => 'walk-in'
            ]
        );

        // Calculate total nights
        $checkIn = Carbon::parse($request->check_in_date);
        $checkOut = Carbon::parse($request->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);

        // Get room types from selected rooms
        $rooms = Room::whereIn('room_id', $request->room_ids)->get();
        $roomTypeId = $rooms->first()->room_type_id;

        // Create reservation
        $reservation = Reservation::create([
            'guest_id' => $guest->guest_id,
            'room_type_id' => $roomTypeId,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'num_guests' => $request->num_guests,
            'total_amount' => $request->total_amount,
            'status' => 'pending',
            'reservation_type' => 'advance',
            'booking_source' => $request->booking_source,
            'special_requests' => $request->special_requests,
            'expires_at' => now()->addDays(2)
        ]);

        // Create ONE booking for the reservation
        $booking = Booking::create([
            'reservation_id' => $reservation->reservation_id,
            'booking_status' => 'reserved',
            'booking_date' => now()
            // NO room_id here anymore!
        ]);

        // Assign MULTIPLE rooms to the booking through booking_rooms
        foreach ($rooms as $room) {
            BookingRoom::create([
                'booking_id' => $booking->booking_id,
                'room_id' => $room->room_id,
                'room_price' => $room->roomType->base_price
            ]);
        }

        // Remove room holds for this session
        RoomHold::where('session_id', session()->getId())->delete();

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully!',
            'reservation_id' => $reservation->reservation_id,
            'booking_id' => $booking->booking_id,
            'rooms_booked' => $rooms->count()
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Reservation creation failed: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to create reservation: ' . $e->getMessage()
        ], 500);
    }
}

    // Update reservation
    public function update(Request $request, Reservation $reservation)
{
    $request->validate([
        'first_name' => 'required|string|max:100',
        'last_name' => 'required|string|max:100',
        'email' => 'required|email|max:150',
        'contact_number' => 'required|string|max:20',
        'check_in_date' => 'required|date',
        'check_out_date' => 'required|date|after:check_in_date',
        'num_guests' => 'required|integer|min:1',
        'total_amount' => 'required|numeric|min:0',
        'status' => 'required|in:pending,confirmed,checked-in,checked-out,cancelled',
        'booking_source' => 'required|in:walk-in,phone,online,agent',
        'special_requests' => 'nullable|string'
    ]);

    DB::beginTransaction();
    try {
        // Update guest information
        $reservation->guest->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_number' => $request->contact_number
        ]);

        // Update reservation
        $reservation->update([
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'num_guests' => $request->num_guests,
            'total_amount' => $request->total_amount,
            'status' => $request->status,
            'booking_source' => $request->booking_source,
            'special_requests' => $request->special_requests
        ]);

        DB::commit();

        return redirect()->route('reservations.index')
            ->with('success', 'Reservation updated successfully!');

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Failed to update reservation: ' . $e->getMessage());
    }
    }
    // Delete reservation
    public function destroy(Reservation $reservation)
    {
        DB::beginTransaction();
        try {
            $reservation->delete();
            DB::commit();
            return redirect()->route('reservations')->with('success', 'Reservation deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete reservation: ' . $e->getMessage());
        }
    }

    // Confirm reservation (move from pending to confirmed)
    public function confirm(Reservation $reservation)
    {
        DB::beginTransaction();
        try {
            $reservation->update([
                'status' => 'confirmed',
                'expires_at' => null // Remove expiration when confirmed
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Reservation confirmed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to confirm reservation: ' . $e->getMessage());
        }
    }

    // Check room availability
    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,room_id',
            'check_in_date' => 'required|date|after:today',
            'check_out_date' => 'required|date|after:check_in_date'
        ]);

        $room = Room::find($request->room_id);
        $isAvailable = $room->isAvailableForDates($request->check_in_date, $request->check_out_date);

        return response()->json([
            'success' => true,
            'available' => $isAvailable
        ]);
    }
  
    // Add these missing methods to your ReservationController
public function edit(Reservation $reservation)
{
    $reservation->load(['guest', 'roomType', 'bookings.rooms.room']);
    $roomTypes = RoomType::all();
    
    return view('reservations.edit', compact('reservation', 'roomTypes'));
}

public function show(Reservation $reservation)
{
    $reservation->load(['guest', 'roomType', 'bookings.rooms.room']);
    return view('reservations.show', compact('reservation'));
}

}