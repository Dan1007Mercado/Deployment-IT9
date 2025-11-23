<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\StripePaymentService;

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

            // Update booking status
            foreach ($reservation->bookings as $booking) {
                $booking->update([
                    'booking_status' => 'reserved'
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

            // Update booking status
            foreach ($reservation->bookings as $booking) {
                $booking->update([
                    'booking_status' => 'reserved'
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

    /**
     * Process online payment with Stripe and generate QR code
     */
    public function processOnlinePayment(Request $request, StripePaymentService $stripeService)
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

            // Create Stripe payment session
            $stripeResult = $stripeService->createPaymentSession($reservation);
            
            if (!$stripeResult['success']) {
                throw new \Exception($stripeResult['error']);
            }

            // Create pending payment record
            $payment = Payment::create([
                'booking_id' => $reservation->bookings->first()->booking_id,
                'amount' => $reservation->total_amount,
                'payment_method' => 'online',
                'payment_status' => 'pending',
                'transaction_id' => $stripeResult['session_id'],
                'stripe_payment_url' => $stripeResult['payment_url'],
                'qr_code_path' => $stripeResult['qr_code'],
                'payment_date' => null
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Online payment session created. Show the QR code to the guest.',
                'payment_url' => $stripeResult['payment_url'],
                'qr_code_url' => asset('storage/' . $stripeResult['qr_code']),
                'session_id' => $stripeResult['session_id'],
                'payment_id' => $payment->payment_id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Online payment initiation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment session: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle successful Stripe payment
     */
    public function stripeSuccess(Request $request, StripePaymentService $stripeService)
    {
        $sessionId = $request->get('session_id');
        
        if ($sessionId) {
            $paymentStatus = $stripeService->checkPaymentStatus($sessionId);
            
            if ($paymentStatus === 'paid') {
                // Find the payment and update it
                $payment = Payment::where('transaction_id', $sessionId)->first();
                
                if ($payment) {
                    DB::beginTransaction();
                    try {
                        // Update payment status
                        $payment->update([
                            'payment_status' => 'completed',
                            'payment_date' => now()
                        ]);

                        // Update reservation status
                        $reservation = $payment->booking->reservation;
                        $reservation->update([
                            'status' => 'confirmed'
                        ]);

                        // Update booking status
                        foreach ($reservation->bookings as $booking) {
                            $booking->update([
                                'booking_status' => 'reserved'
                            ]);
                        }

                        DB::commit();

                        return view('payments.stripe-success', [
                            'reservation' => $reservation,
                            'payment' => $payment
                        ]);

                    } catch (\Exception $e) {
                        DB::rollBack();
                        \Log::error('Stripe success processing error: ' . $e->getMessage());
                        return view('payments.stripe-error', [
                            'message' => 'Failed to process payment confirmation: ' . $e->getMessage()
                        ]);
                    }
                }
            }
        }

        return redirect()->route('payment.stripe.cancel');
    }

    /**
     * Handle cancelled Stripe payment
     */
    public function stripeCancel(Request $request)
    {
        $reservationId = $request->get('reservation_id');
        $reservation = null;
        
        if ($reservationId) {
            $reservation = Reservation::find($reservationId);
        }
        
        return view('payments.stripe-cancel', [
            'reservation' => $reservation
        ]);
    }

    /**
     * Get QR code for existing online payment
     */
    public function getPaymentQRCode(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,reservation_id'
        ]);

        try {
            $reservation = Reservation::find($request->reservation_id);
            $payment = $reservation->bookings->first()->payments()
                ->where('payment_method', 'online')
                ->where('payment_status', 'pending')
                ->latest()
                ->first();

            if ($payment && $payment->qr_code_path) {
                return response()->json([
                    'success' => true,
                    'qr_code_url' => asset('storage/' . $payment->qr_code_path),
                    'payment_url' => $payment->stripe_payment_url,
                    'session_id' => $payment->transaction_id
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No pending online payment found for this reservation'
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Get QR code error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check payment status for a pending online payment
     */
    public function checkPaymentStatus(Request $request, StripePaymentService $stripeService)
    {
        $request->validate([
            'session_id' => 'required|string',
            'reservation_id' => 'required|exists:reservations,reservation_id'
        ]);

        try {
            $paymentStatus = $stripeService->checkPaymentStatus($request->session_id);
            $reservation = Reservation::find($request->reservation_id);
            
            if ($paymentStatus === 'paid') {
                // Update payment and reservation status
                $payment = Payment::where('transaction_id', $request->session_id)->first();
                
                if ($payment) {
                    DB::beginTransaction();
                    
                    $payment->update([
                        'payment_status' => 'completed',
                        'payment_date' => now()
                    ]);

                    $reservation->update([
                        'status' => 'confirmed'
                    ]);

                    foreach ($reservation->bookings as $booking) {
                        $booking->update([
                            'booking_status' => 'reserved'
                        ]);
                    }

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'payment_status' => 'paid',
                        'message' => 'Payment completed successfully!'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'payment_status' => $paymentStatus,
                'message' => $paymentStatus === 'unpaid' ? 'Payment is still pending' : 'Payment status: ' . $paymentStatus
            ]);

        } catch (\Exception $e) {
            \Log::error('Check payment status error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook handler for Stripe events (recommended for production)
     */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            \Log::error('Stripe webhook invalid payload: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            \Log::error('Stripe webhook invalid signature: ' . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCompletedSession($session);
                break;
            case 'checkout.session.async_payment_succeeded':
                $session = $event->data->object;
                $this->handleCompletedSession($session);
                break;
            case 'checkout.session.expired':
                $session = $event->data->object;
                $this->handleExpiredSession($session);
                break;
            default:
                \Log::info('Received unhandled event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle completed Stripe session
     */
    private function handleCompletedSession($session)
    {
        DB::beginTransaction();
        try {
            $payment = Payment::where('transaction_id', $session->id)->first();
            
            if ($payment && $payment->payment_status === 'pending') {
                $payment->update([
                    'payment_status' => 'completed',
                    'payment_date' => now()
                ]);

                $reservation = $payment->booking->reservation;
                $reservation->update([
                    'status' => 'confirmed'
                ]);

                foreach ($reservation->bookings as $booking) {
                    $booking->update([
                        'booking_status' => 'reserved'
                    ]);
                }

                DB::commit();
                \Log::info('Payment completed via webhook for session: ' . $session->id);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Webhook session completion error: ' . $e->getMessage());
        }
    }

    /**
     * Handle expired Stripe session
     */
    private function handleExpiredSession($session)
    {
        try {
            $payment = Payment::where('transaction_id', $session->id)->first();
            
            if ($payment && $payment->payment_status === 'pending') {
                $payment->update([
                    'payment_status' => 'expired'
                ]);

                \Log::info('Payment expired via webhook for session: ' . $session->id);
            }
        } catch (\Exception $e) {
            \Log::error('Webhook session expiration error: ' . $e->getMessage());
        }
    }
}