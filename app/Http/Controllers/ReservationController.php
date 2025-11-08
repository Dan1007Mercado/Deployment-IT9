<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Guest;
use App\Models\Room;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['guest', 'room'])->get();
        return view('reservations', compact('reservations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_first_name' => 'required|string',
            'guest_last_name' => 'required|string',
            'email' => 'required|email',
            'room' => 'required|string',
            'room_type' => 'required|string',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after_or_equal:check_in',
            'nights' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
            'status' => 'nullable|string',
        ]);

        // Find or create guest
        $guest = Guest::firstOrCreate(
            [
                'email' => $validated['email']
            ],
            [
                'name' => $validated['guest_first_name'] . ' ' . $validated['guest_last_name']
            ]
        );

        // Find or create room
        $room = Room::firstOrCreate(
            [
                'number' => $validated['room']
            ],
            [
                'type' => $validated['room_type'],
                'price' => $validated['price'],
                'status' => 'available'
            ]
        );

        // Create reservation
        Reservation::create([
            'guest_id' => $guest->id,
            'room_id' => $room->id,
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'status' => $validated['status'] ?? 'Checked In',
        ]);

        return redirect()->back()->with('success', 'Reservation created!');
    }
}