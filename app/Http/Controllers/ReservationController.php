<?php

    namespace App\Http\Controllers;

    use App\Models\Reservation;
    use App\Models\Guest;
    use App\Models\Room;
    use App\Models\RoomType;
    use App\Models\Booking;
    use App\Models\BookingRoom;
    use App\Models\RoomHold;
    use App\Models\Payment;
    use App\Services\GmailService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Log;

    class ReservationController extends Controller
    {
        protected $gmailService;

        public function __construct()
        {
            $this->gmailService = new GmailService();
        }

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

        public function getPaymentDetails(Reservation $reservation)
        {
            try {
                Log::info('Payment details requested for reservation: ' . $reservation->reservation_id);
                
                // Eager load the relationships
                $reservation->load(['guest', 'roomType', 'bookings.rooms.room']);
                
                // Get room numbers
                $roomNumbers = [];
                foreach($reservation->bookings as $booking) {
                    foreach($booking->rooms as $bookingRoom) {
                        $roomNumbers[] = $bookingRoom->room->room_number;
                    }
                }
                $roomNumbers = array_unique($roomNumbers);
                
                return response()->json([
                    'success' => true,
                    'reservation' => [
                        'reservation_id' => $reservation->reservation_id,
                        'guest' => [
                            'first_name' => $reservation->guest->first_name,
                            'last_name' => $reservation->guest->last_name,
                            'email' => $reservation->guest->email,
                            'contact_number' => $reservation->guest->contact_number,
                        ],
                        'check_in_date' => $reservation->check_in_date->format('M j, Y'),
                        'check_out_date' => $reservation->check_out_date->format('M j, Y'),
                        'nights' => $reservation->check_in_date->diffInDays($reservation->check_out_date),
                        'total_amount' => $reservation->total_amount,
                        'room_numbers' => implode(', ', $roomNumbers),
                    ]
                ]);
                
            } catch (\Exception $e) {
                Log::error('Error in getPaymentDetails: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                
                return response()->json([
                    'success' => false,
                    'message' => 'Error loading reservation: ' . $e->getMessage()
                ], 500);
            }
        }

        // Get available rooms for selected dates and room type
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

        // Check email availability and name conflict
        public function checkEmail(Request $request)
        {
            $request->validate([
                'email' => 'required|email',
                'first_name' => 'nullable|string',
                'last_name' => 'nullable|string'
            ]);
            
            $existingGuest = Guest::where('email', $request->email)->first();
            
            if ($existingGuest) {
                $existingName = $existingGuest->first_name . ' ' . $existingGuest->last_name;
                
                // If name is provided in the request, check if it matches
                if ($request->has('first_name') && $request->has('last_name')) {
                    $newName = $request->first_name . ' ' . $request->last_name;
                    
                    if (strtolower(trim($existingName)) !== strtolower(trim($newName))) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Email conflict detected',
                            'conflict' => true,
                            'existing_guest' => [
                                'name' => $existingName,
                                'id' => $existingGuest->guest_id
                            ],
                            'error_message' => 'This email is already registered. Please use different one '
                        ]);
                    }
                }
                
                return response()->json([
                    'success' => true,
                    'exists' => true,
                    'guest_name' => $existingName
                ]);
            }
            
            return response()->json([
                'success' => true,
                'exists' => false
            ]);
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

            // ADD EMAIL VALIDATION CHECK
            $existingGuest = Guest::where('email', $request->email)->first();
            
            if ($existingGuest) {
                // Check if the name matches the existing guest
                $existingName = strtolower(trim($existingGuest->first_name . ' ' . $existingGuest->last_name));
                $newName = strtolower(trim($request->first_name . ' ' . $request->last_name));
                
                if ($existingName !== $newName) {
                    // Names don't match - return validation error
                    return response()->json([
                        'success' => false,
                        'message' => 'Email already registered to a different person',
                        'errors' => [
                            'email' => [
                                'This email is already registered to ' . 
                                $existingGuest->first_name . ' ' . $existingGuest->last_name . 
                                '. Please use a different email or verify the name.'
                            ]
                        ]
                    ], 422);
                }
            }

            DB::beginTransaction();
            try {
                // If email exists with same name, use existing guest
                if ($existingGuest && 
                    strtolower(trim($existingGuest->first_name)) === strtolower(trim($request->first_name)) &&
                    strtolower(trim($existingGuest->last_name)) === strtolower(trim($request->last_name))) {
                    
                    $guest = $existingGuest;
                    
                    // Update contact number if changed
                    if ($guest->contact_number !== $request->contact_number) {
                        $guest->update(['contact_number' => $request->contact_number]);
                    }
                } else {
                    // Create new guest
                    $guest = Guest::create([
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'contact_number' => $request->contact_number,
                        'guest_type' => 'walk-in'
                    ]);
                }

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

                // TRY TO SEND EMAIL (BUT DON'T BREAK IF IT FAILS)
                $emailSent = false;
                try {
                    // Load relationships for email
                    $reservation->load(['guest', 'roomType', 'bookings.rooms.room']);
                    $emailSent = $this->gmailService->sendReservationCreatedEmail($reservation, $reservation->guest);
                } catch (\Exception $e) {
                    Log::error('Email failed but reservation created: ' . $e->getMessage());
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Reservation created successfully!' . ($emailSent ? ' Email sent.' : ''),
                    'reservation_id' => $reservation->reservation_id,
                    'booking_id' => $booking->booking_id,
                    'rooms_booked' => $rooms->count(),
                    'guest_name' => $guest->first_name . ' ' . $guest->last_name,
                    'email' => $guest->email
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Reservation creation failed: ' . $e->getMessage());
                Log::error($e->getTraceAsString());
                
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

            // Check if email is being changed to one that belongs to a different guest
            if ($request->email !== $reservation->guest->email) {
                $existingGuest = Guest::where('email', $request->email)->first();
                
                if ($existingGuest && $existingGuest->guest_id !== $reservation->guest_id) {
                    $existingName = strtolower(trim($existingGuest->first_name . ' ' . $existingGuest->last_name));
                    $newName = strtolower(trim($request->first_name . ' ' . $request->last_name));
                    
                    if ($existingName !== $newName) {
                        return back()->withErrors([
                            'email' => 'This email is already registered to ' . 
                                    $existingGuest->first_name . ' ' . $existingGuest->last_name
                        ]);
                    }
                }
            }

            DB::beginTransaction();
            try {
                // Check if guest details match existing record or need update
                $guest = $reservation->guest;
                
                if ($request->email === $guest->email && 
                    strtolower(trim($request->first_name)) === strtolower(trim($guest->first_name)) &&
                    strtolower(trim($request->last_name)) === strtolower(trim($guest->last_name))) {
                    
                    // Same guest, just update contact number if changed
                    if ($guest->contact_number !== $request->contact_number) {
                        $guest->update(['contact_number' => $request->contact_number]);
                    }
                } else {
                    // Different name or email - need to find or create new guest
                    $existingGuest = Guest::where('email', $request->email)->first();
                    
                    if ($existingGuest && 
                        strtolower(trim($existingGuest->first_name)) === strtolower(trim($request->first_name)) &&
                        strtolower(trim($existingGuest->last_name)) === strtolower(trim($request->last_name))) {
                        
                        // Update existing guest with new contact number
                        $existingGuest->update([
                            'contact_number' => $request->contact_number
                        ]);
                        
                        // Update reservation to use this guest
                        $reservation->update(['guest_id' => $existingGuest->guest_id]);
                    } else {
                        // Create new guest
                        $newGuest = Guest::create([
                            'first_name' => $request->first_name,
                            'last_name' => $request->last_name,
                            'email' => $request->email,
                            'contact_number' => $request->contact_number,
                            'guest_type' => $guest->guest_type // Preserve guest type
                        ]);
                        
                        // Update reservation to use new guest
                        $reservation->update(['guest_id' => $newGuest->guest_id]);
                    }
                }

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