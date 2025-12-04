<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Payment;
use App\Services\GmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class GuestBookingController extends Controller
{
    protected $gmailService;

    public function __construct()
    {
        $this->gmailService = new GmailService();
    }

    public function home()
    {
        \Log::info('Loading home page with room types');
        
        $roomTypes = RoomType::with(['rooms' => function($query) {
            $query->where('room_status', 'available')->limit(5);
        }])
        ->withCount(['rooms' => function($query) {
            $query->where('room_status', 'available');
        }])
        ->get();
        
        \Log::info('Found ' . $roomTypes->count() . ' room types');
        
        return view('home', compact('roomTypes'));
    }

    public function index()
    {
        \Log::info('Hotel index page accessed');
        return $this->home();
    }

    public function checkAvailability(Request $request)
    {
        try {
            \Log::info('Guest check availability request started', $request->all());
            
            $request->validate([
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'room_type_id' => 'required|exists:room_types,room_type_id',
                'num_rooms' => 'required|integer|min:1'
            ]);

            \Log::info('Validation passed for availability check');
            
            $availableRooms = Room::where('room_type_id', $request->room_type_id)
                ->where('room_status', 'available')
                ->whereDoesntHave('bookings', function($query) use ($request) {
                    \Log::info('Executing whereDoesntHave query');
                    $query->whereHas('booking.reservation', function($q) use ($request) {
                        $q->where(function($q2) use ($request) {
                            $q2->where('check_in_date', '<', $request->check_out_date)
                               ->where('check_out_date', '>', $request->check_in_date);
                        })
                        ->whereIn('status', ['confirmed', 'checked_in', 'reserved']);
                    });
                })
                ->limit($request->num_rooms)
                ->get();

            \Log::info('Found ' . $availableRooms->count() . ' available rooms out of ' . $request->num_rooms . ' needed');

            $roomType = RoomType::find($request->room_type_id);
            if (!$roomType) {
                \Log::error('Room type not found: ' . $request->room_type_id);
                return response()->json([
                    'success' => false,
                    'message' => 'Room type not found'
                ], 404);
            }
            
            $checkIn = Carbon::parse($request->check_in_date);
            $checkOut = Carbon::parse($request->check_out_date);
            $nights = $checkIn->diffInDays($checkOut);
            
            \Log::info('Check-in: ' . $checkIn . ', Check-out: ' . $checkOut . ', Nights: ' . $nights);
            \Log::info('Room price: ' . $roomType->base_price . ', Total amount: ' . ($roomType->base_price * $nights * $request->num_rooms));

            return response()->json([
                'success' => true,
                'available_rooms' => $availableRooms->count(),
                'rooms_needed' => $request->num_rooms,
                'is_available' => $availableRooms->count() >= $request->num_rooms,
                'room_type' => $roomType,
                'nights' => $nights,
                'total_amount' => $roomType->base_price * $nights * $request->num_rooms,
                'available_rooms_list' => $availableRooms->map(function($room) {
                    return [
                        'room_id' => $room->room_id,
                        'room_number' => $room->room_number,
                        'floor' => $room->floor,
                    ];
                })
            ]);

        } catch (\Exception $e) {
            \Log::error('Check availability error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error checking availability: ' . $e->getMessage()
            ], 500);
        }
    }

    public function roomDetails($roomTypeId)
    {
        \Log::info('Loading room details for room type: ' . $roomTypeId);
        
        $roomType = RoomType::with(['rooms' => function($query) {
            $query->where('room_status', 'available');
        }])
        ->withCount(['rooms' => function($query) {
            $query->where('room_status', 'available');
        }])
        ->findOrFail($roomTypeId);
        
        $similarRooms = RoomType::where('room_type_id', '!=', $roomTypeId)
            ->where('is_active', true)
            ->limit(3)
            ->get();

        \Log::info('Found room type: ' . $roomType->type_name . ' with ' . $roomType->rooms_count . ' available rooms');
        
        return view('guest.room-details', compact('roomType', 'similarRooms'));
    }

    public function prepareBooking(Request $request)
    {
        try {
            \Log::info('Prepare booking request started', $request->all());
            
            $request->validate([
                'room_type_id' => 'required|exists:room_types,room_type_id',
                'check_in_date' => 'required|date|after_or_equal:today',
                'check_out_date' => 'required|date|after:check_in_date',
                'num_rooms' => 'required|integer|min:1|max:5',
                'num_guests' => 'required|integer|min:1|max:20'
            ]);

            \Log::info('Validation passed for prepare booking');
            
            $roomType = RoomType::findOrFail($request->room_type_id);
            $checkIn = Carbon::parse($request->check_in_date);
            $checkOut = Carbon::parse($request->check_out_date);
            $nights = $checkIn->diffInDays($checkOut);
            $totalAmount = $roomType->base_price * $nights * $request->num_rooms;
            
            \Log::info('Room type: ' . $roomType->room_type_name . ', Nights: ' . $nights . ', Total: ' . $totalAmount);

            $availableRooms = Room::where('room_type_id', $request->room_type_id)
                ->where('room_status', 'available')
                ->whereDoesntHave('bookings', function($query) use ($request) {
                    $query->whereHas('booking.reservation', function($q) use ($request) {
                        $q->where(function($q2) use ($request) {
                            $q2->where('check_in_date', '<', $request->check_out_date)
                               ->where('check_out_date', '>', $request->check_in_date);
                        })
                        ->whereIn('status', ['confirmed', 'checked_in', 'reserved']);
                    });
                })
                ->limit($request->num_rooms)
                ->get();

            \Log::info('Available rooms count for booking: ' . $availableRooms->count() . ', Needed: ' . $request->num_rooms);

            if ($availableRooms->count() < $request->num_rooms) {
                \Log::warning('Insufficient available rooms for booking');
                return response()->json([
                    'success' => false,
                    'message' => 'Sorry, the selected rooms are no longer available. Please try different dates or room type.'
                ], 400);
            }

            $tempReference = 'TEMP-' . strtoupper(uniqid());
            \Log::info('Generated temp reference: ' . $tempReference);

            session([
                'temp_booking' => [
                    'reference' => $tempReference,
                    'room_type_id' => $roomType->room_type_id,
                    'room_type_name' => $roomType->room_type_name,
                    'check_in_date' => $request->check_in_date,
                    'check_out_date' => $request->check_out_date,
                    'num_rooms' => $request->num_rooms,
                    'num_guests' => $request->num_guests,
                    'nights' => $nights,
                    'room_price' => $roomType->base_price,
                    'total_amount' => $totalAmount,
                    'available_room_ids' => $availableRooms->pluck('room_id')->toArray(),
                    'expires_at' => Carbon::now()->addMinutes(30)
                ]
            ]);

            \Log::info('Temp booking stored in session, expires at: ' . Carbon::now()->addMinutes(30));

            return response()->json([
                'success' => true,
                'temp_reference' => $tempReference,
                'booking_summary' => [
                    'room_type' => $roomType->room_type_name,
                    'check_in' => $request->check_in_date,
                    'check_out' => $request->check_out_date,
                    'nights' => $nights,
                    'num_rooms' => $request->num_rooms,
                    'num_guests' => $request->num_guests,
                    'room_price' => number_format($roomType->base_price, 2),
                    'subtotal' => number_format($roomType->base_price * $nights * $request->num_rooms, 2),
                    'tax' => number_format(0, 2),
                    'total' => number_format($totalAmount, 2)
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Prepare booking error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error preparing booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function confirmBooking(Request $request)
    {
        try {
            \Log::info('Confirm booking request started', $request->except(['first_name', 'last_name', 'email', 'contact_number']));
            
            $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => 'required|email|max:150',
                'contact_number' => 'required|string|max:20',
                'special_requests' => 'nullable|string|max:500',
                'payment_method' => 'required|in:online,credit_card',
                'temp_reference' => 'required|string'
            ]);

            \Log::info('Validation passed for confirm booking');

            $tempBooking = session('temp_booking');
            
            \Log::info('Temp booking from session:', $tempBooking ?? ['no_temp_booking' => true]);
            
            if (!$tempBooking || $tempBooking['reference'] !== $request->temp_reference) {
                \Log::warning('Invalid temp booking reference or no session');
                return response()->json([
                    'success' => false,
                    'message' => 'Booking session expired or invalid. Please start over.'
                ], 400);
            }

            if (Carbon::parse($tempBooking['expires_at'])->isPast()) {
                \Log::warning('Temp booking expired');
                session()->forget('temp_booking');
                return response()->json([
                    'success' => false,
                    'message' => 'Booking session has expired. Please start over.'
                ], 400);
            }

            \Log::info('Starting database transaction for booking confirmation');
            DB::beginTransaction();
            
            try {
                $guest = Guest::where('email', $request->email)->first();
                
                if (!$guest) {
                    \Log::info('Creating new guest');
                    $guest = Guest::create([
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'email' => $request->email,
                        'contact_number' => $request->contact_number,
                        'guest_type' => 'advance'   
                    ]);
                    \Log::info('Guest created with ID: ' . $guest->guest_id);
                } else {
                    \Log::info('Updating existing guest ID: ' . $guest->guest_id);
                    $guest->update([
                        'first_name' => $request->first_name,
                        'last_name' => $request->last_name,
                        'contact_number' => $request->contact_number
                    ]);
                }

                \Log::info('Creating reservation for guest ID: ' . $guest->guest_id);
                $reservation = Reservation::create([
                    'guest_id' => $guest->guest_id,
                    'room_type_id' => $tempBooking['room_type_id'],
                    'check_in_date' => $tempBooking['check_in_date'],
                    'check_out_date' => $tempBooking['check_out_date'],
                    'num_guests' => $tempBooking['num_guests'],
                    'total_amount' => $tempBooking['total_amount'],
                    'status' => 'Completed',
                    'reservation_type' => 'advance',
                    'booking_source' => 'online',
                    'special_requests' => $request->special_requests,
                    'expires_at' => Carbon::now()->addHours(24)
                ]);

                \Log::info('Reservation created with ID: ' . $reservation->reservation_id);

                \Log::info('Creating booking for reservation ID: ' . $reservation->reservation_id);
                $booking = Booking::create([
                    'reservation_id' => $reservation->reservation_id,
                    'booking_status' => $request->payment_method === 'credit_card' ? 'reserved' : 'completed',
                    'booking_date' => now()
                ]);

                \Log::info('Booking created with ID: ' . $booking->booking_id);

                $rooms = Room::whereIn('room_id', array_slice($tempBooking['available_room_ids'], 0, $tempBooking['num_rooms']))
                    ->get();
                
                \Log::info('Assigning ' . $rooms->count() . ' rooms to booking');
                
                foreach ($rooms as $room) {
                    BookingRoom::create([
                        'booking_id' => $booking->booking_id,
                        'room_id' => $room->room_id,
                        'room_price' => $tempBooking['room_price']
                    ]);
                    
                    \Log::info('Room ' . $room->room_number . ' assigned to booking, updating status to reserved');
                    
                    $room->update(['room_status' => 'occupied']);
                }

                $transactionId = 'BOOK-' . strtoupper(uniqid());
                \Log::info('Generated transaction ID: ' . $transactionId);

                $payment = Payment::create([
                    'booking_id' => $booking->booking_id,
                    'amount' => $tempBooking['total_amount'],
                    'payment_method' => $request->payment_method,
                    'payment_status' => $request->payment_method === 'credit_card' ? 'completed' : 'completed',
                    'transaction_id' => $transactionId,
                    'payment_date' => $request->payment_method === 'credit_card' ? null : now(),
                    'notes' => $request->payment_method === 'credit_card' 
                ]);

                \Log::info('Payment created with ID: ' . $payment->payment_id . ', Status: ' . $payment->payment_status);

                $reservation->update([
                    'status' => $request->payment_method === 'credit_card' ? 'confirmed' : 'confirmed'
                ]);

                \Log::info('Reservation status updated to: ' . $reservation->status);

                DB::commit();
                DB::commit();
\Log::info('Database transaction committed successfully');

// DEBUG: Check email service
\Log::info('DEBUG: GmailService authenticated: ' . ($this->gmailService->isAuthenticated() ? 'YES' : 'NO'));
\Log::info('DEBUG: Guest email: ' . $reservation->guest->email);

// SEND EMAILS HERE - AFTER DB COMMIT
try {
    $reservation->refresh()->load(['guest', 'roomType', 'bookings.rooms.room', 'payments']);
    
    \Log::info('DEBUG: Starting to send email...');
    
    if ($request->payment_method === 'online') {
        $emailSent = $this->gmailService->sendOnlineBookingConfirmation($reservation, $reservation->guest, $payment);
        \Log::info('DEBUG: Online payment email sent: ' . ($emailSent ? 'YES' : 'NO'));
    } elseif ($request->payment_method === 'credit_card') {
        $emailSent = $this->gmailService->sendCreditCardBookingConfirmation($reservation, $reservation->guest, $payment);
        \Log::info('DEBUG: Credit Card email sent: ' . ($emailSent ? 'YES' : 'NO'));
    }
    
    if (!$emailSent) {
        \Log::error('DEBUG: EMAIL FAILED - Check GmailService logs');
    }
} catch (\Exception $e) {
    \Log::error('DEBUG: Email exception: ' . $e->getMessage());
}
                \Log::info('Database transaction committed successfully');

                // SEND EMAILS HERE - AFTER DB COMMIT
                try {
                    $reservation->refresh()->load(['guest', 'roomType', 'bookings.rooms.room']);
                    
                    if ($request->payment_method === 'online') {
                        $emailSent = $this->gmailService->sendOnlineBookingConfirmation($reservation, $reservation->guest, $payment);
                        \Log::info('Online payment confirmation email sent: ' . ($emailSent ? 'YES' : 'NO'));
                    } elseif ($request->payment_method === 'credit_card') {
                        $emailSent = $this->gmailService->sendCreditCardBookingConfirmation($reservation, $reservation->guest, $payment);
                        \Log::info('Credit Card booking confirmation email sent: ' . ($emailSent ? 'YES' : 'NO'));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send email: ' . $e->getMessage());
                }

                session()->forget('temp_booking');
                \Log::info('Temp booking cleared from session');

                if ($request->payment_method === 'online') {
                    \Log::info('Creating Stripe session for online payment');
                    return $this->createStripeSession($payment, $reservation);
                }

                \Log::info('Credit Card payment booking completed successfully');
                return response()->json([
                    'success' => true,
                    'message' => 'Booking confirmed!',
                    'booking_reference' => $transactionId,
                    'reservation_id' => $reservation->reservation_id,
                    'payment_method' => 'credit_card',
                    'redirect_url' => route('guest.booking.success', ['reservation' => $reservation->reservation_id])
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Booking creation error in transaction:', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create booking: ' . $e->getMessage()
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Confirm booking outer error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processOnlinePayment(Request $request)
    {
        try {
            \Log::info('Process online payment request', $request->all());
            
            $request->validate([
                'reservation_id' => 'required|exists:reservations,reservation_id',
                'payment_method' => 'required|in:stripe,paypal,gcash'
            ]);

            \Log::info('Processing online payment for reservation: ' . $request->reservation_id);

            $reservation = Reservation::with(['payments', 'guest'])->findOrFail($request->reservation_id);
            
            $existingPayment = $reservation->payments()->where('payment_method', 'online')->first();
            
            if ($existingPayment) {
                \Log::info('Using existing payment ID: ' . $existingPayment->payment_id);
                return $this->createStripeSession($existingPayment, $reservation);
            }

            $transactionId = 'BOOK-' . strtoupper(uniqid());
            \Log::info('Creating new payment with transaction ID: ' . $transactionId);
            
            $payment = Payment::create([
                'booking_id' => $reservation->bookings()->first()->booking_id,
                'amount' => $reservation->total_amount,
                'payment_method' => 'online',
                'payment_status' => 'completed',
                'transaction_id' => $transactionId,
                'payment_date' => null
            ]);

            \Log::info('New payment created with ID: ' . $payment->payment_id);
            return $this->createStripeSession($payment, $reservation);

        } catch (\Exception $e) {
            \Log::error('Process online payment error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error processing online payment: ' . $e->getMessage()
            ], 500);
        }
    }

    private function createStripeSession($payment, $reservation)
    {
        try {
            \Log::info('Creating Stripe session for payment ID: ' . $payment->payment_id);
            
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'php',
                        'product_data' => [
                            'name' => 'Hotel Booking - ' . $reservation->roomType->room_type_name,
                            'description' => 'Check-in: ' . $reservation->check_in_date . 
                                           ' | Check-out: ' . $reservation->check_out_date .
                                           ' | Guests: ' . $reservation->num_guests,
                        ],
                        'unit_amount' => $payment->amount * 100,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('guest.booking.success', ['reservation' => $reservation->reservation_id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('guest.booking.cancel', ['reservation' => $reservation->reservation_id]),
                'metadata' => [
                    'reservation_id' => $reservation->reservation_id,
                    'payment_id' => $payment->payment_id,
                    'transaction_id' => $payment->transaction_id
                ],
                'customer_email' => $reservation->guest->email,
            ]);

            \Log::info('Stripe session created with ID: ' . $checkout_session->id);

            $payment->update([
                'stripe_session_id' => $checkout_session->id,
                'stripe_payment_intent_id' => $checkout_session->payment_intent
            ]);

            \Log::info('Payment updated with Stripe session ID');

            return response()->json([
                'success' => true,
                'session_id' => $checkout_session->id,
                'redirect_url' => $checkout_session->url
            ]);

        } catch (\Exception $e) {
            \Log::error('Stripe session creation error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stripe_key_exists' => !empty(env('STRIPE_SECRET'))
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating payment session: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bookingSuccess($reservationId)
    {
        try {
            \Log::info('Loading booking success page for reservation: ' . $reservationId);
            
            $reservation = Reservation::with(['guest', 'roomType', 'payments', 'bookings.rooms.room'])
                ->findOrFail($reservationId);

            \Log::info('Reservation loaded, status: ' . $reservation->status);

            $sessionId = request()->query('session_id');
            if ($sessionId) {
                \Log::info('Processing Stripe callback with session ID: ' . $sessionId);
                
                try {
                    Stripe::setApiKey(env('STRIPE_SECRET'));
                    $session = Session::retrieve($sessionId);
                    
                    \Log::info('Stripe session retrieved, payment status: ' . $session->payment_status);
                    
                    if ($session->payment_status === 'paid') {
                        $payment = $reservation->payments()->where('stripe_session_id', $sessionId)->first();
                        if ($payment) {
                            \Log::info('Updating payment ID: ' . $payment->payment_id . ' to completed');
                            
                            $payment->update([
                                'payment_status' => 'completed',
                                'payment_date' => now(),
                                'stripe_payment_intent_id' => $session->payment_intent
                            ]);
                            
                            $reservation->update(['status' => 'confirmed']);
                            \Log::info('Reservation status updated to confirmed');
                            
                            if ($reservation->bookings()->exists()) {
                                $reservation->bookings()->update(['booking_status' => 'confirmed']);
                                \Log::info('Booking status updated to confirmed');
                            }
                            
                            try {
                                $reservation->refresh()->load(['guest', 'roomType', 'bookings.rooms.room', 'payments']);
                                $emailSent = $this->gmailService->sendOnlineBookingConfirmation($reservation, $reservation->guest, $payment);
                                \Log::info('Payment completion email sent: ' . ($emailSent ? 'YES' : 'NO'));
                            } catch (\Exception $e) {
                                \Log::error('Failed to send payment completion email: ' . $e->getMessage());
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Stripe session retrieval error: ' . $e->getMessage());
                }
            }

            return view('guest.booking-success', compact('reservation'));

        } catch (\Exception $e) {
            \Log::error('Booking success page error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            abort(404);
        }
    }

    public function bookingCancel($reservationId)
    {
        try {
            \Log::info('Loading booking cancel page for reservation: ' . $reservationId);
            
            $reservation = Reservation::with(['guest', 'roomType'])
                ->findOrFail($reservationId);

            \Log::info('Reservation current status: ' . $reservation->status);

            if (in_array($reservation->status, ['pending', 'reserved'])) {
                \Log::info('Cancelling reservation and releasing rooms');
                
                $reservation->update(['status' => 'cancelled']);
                
                foreach ($reservation->bookings as $booking) {
                    foreach ($booking->rooms as $bookingRoom) {
                        $room = $bookingRoom->room;
                        $room->update(['room_status' => 'available']);
                        \Log::info('Room ' . $room->room_number . ' released to available');
                    }
                }
                
                \Log::info('Reservation cancelled successfully');
            }

            return view('guest.booking-cancel', compact('reservation'));

        } catch (\Exception $e) {
            \Log::error('Booking cancel page error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            abort(404);
        }
    }

    public function processPayment(Request $request)
    {
        \Log::info('Process payment from modal called');
        return $this->confirmBooking($request);
    }

    
}