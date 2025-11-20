<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function getPaymentDetails(Reservation $reservation)
    {
        try {
            $reservation->load(['guest', 'roomType', 'bookings.rooms.room']);
            
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
                    'nights' => $reservation->nights,
                    'total_amount' => $reservation->total_amount,
                    'room_numbers' => $reservation->room_numbers,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load reservation details'
            ], 500);
        }
    }

    public function processCashPayment(Request $request)
{
    $request->validate([
        'reservation_id' => 'required|exists:reservations,reservation_id',
        'amount_paid' => 'required|numeric|min:0'
    ]);

    DB::beginTransaction();
    try {
        $reservation = Reservation::findOrFail($request->reservation_id);
        
        if ($reservation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Reservation is not in pending status'
            ], 400);
        }

        $paidAmount = floatval($request->amount_paid);
        $totalAmount = floatval($reservation->total_amount);

        // Update reservation status
        $reservation->update([
            'status' => 'confirmed'
        ]);

        // Update booking status - FIXED: Added quotes
        foreach ($reservation->bookings as $booking) {
            $booking->update([
                'booking_status' => 'reserved'  // ✅ Fixed
            ]);
        }

        // Create payment record
        $payment = Payment::create([
            'booking_id' => $reservation->bookings->first()->booking_id,
            'amount' => $paidAmount,
            'payment_method' => 'cash',
            'payment_status' => 'completed',
            'transaction_id' => 'CASH-' . time() . '-' . $reservation->reservation_id,
            'payment_date' => now()
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Reservation confirmed! Cash payment recorded.',
            'payment_id' => $payment->payment_id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Cash payment error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to process payment: ' . $e->getMessage()
        ], 500);
    }
}

public function processCardPayment(Request $request)
{
    $request->validate([
        'reservation_id' => 'required|exists:reservations,reservation_id'
    ]);

    DB::beginTransaction();
    try {
        $reservation = Reservation::findOrFail($request->reservation_id);
        
        if ($reservation->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Reservation is not in pending status'
            ], 400);
        }

        // Update reservation status
        $reservation->update([
            'status' => 'confirmed'
        ]);

        // Update booking status - FIXED: Added quotes
        foreach ($reservation->bookings as $booking) {
            $booking->update([
                'booking_status' => 'reserved'  // ✅ Fixed
            ]);
        }

        // Create payment record
        $payment = Payment::create([
            'booking_id' => $reservation->bookings->first()->booking_id,
            'amount' => $reservation->total_amount,
            'payment_method' => 'credit_card',
            'payment_status' => 'completed',
            'transaction_id' => 'CARD-' . time() . '-' . $reservation->reservation_id,
            'sandbox_reference' => 'DEMO-REF-' . uniqid(),
            'payment_date' => now()
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Reservation confirmed! Card payment simulated.',
            'payment_id' => $payment->payment_id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Card payment error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to process payment: ' . $e->getMessage()
        ], 500);
    }
}
}