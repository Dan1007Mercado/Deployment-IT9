<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createPaymentSession($reservation)
    {
        try {
            // Get room numbers from relationships
            $roomNumbers = [];
            foreach($reservation->bookings as $booking) {
                foreach($booking->rooms as $bookingRoom) {
                    $roomNumbers[] = $bookingRoom->room->room_number;
                }
            }
            $roomNumbersString = implode(', ', array_unique($roomNumbers));

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'php',
                        'product_data' => [
                            'name' => 'Hotel Reservation #' . $reservation->reservation_id,
                            'description' => "Guest: {$reservation->guest->first_name} {$reservation->guest->last_name}\nCheck-in: {$reservation->check_in_date->format('M d, Y')}\nCheck-out: {$reservation->check_out_date->format('M d, Y')}\nRooms: {$roomNumbersString}",
                        ],
                        'unit_amount' => $reservation->total_amount * 100, // Convert to cents
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payments.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payments.stripe.cancel') . '?reservation_id=' . $reservation->reservation_id,
                'customer_email' => $reservation->guest->email,
                'metadata' => [
                    'reservation_id' => $reservation->reservation_id,
                    'guest_name' => $reservation->guest->first_name . ' ' . $reservation->guest->last_name
                ],
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'payment_url' => $session->url,
                'amount' => $reservation->total_amount
                // REMOVED: 'qr_code' => $qrCode,
            ];

        } catch (\Exception $e) {
            Log::error('Stripe session creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function checkPaymentStatus(string $sessionId)
    {
        try {
            $session = Session::retrieve($sessionId);
            return $session->payment_status;
        } catch (\Exception $e) {
            Log::error('Error checking payment status: ' . $e->getMessage());
            return null;
        }
    }
}