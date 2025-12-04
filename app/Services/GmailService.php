<?php
// app/Services/GmailService.php

namespace App\Services;

use Google_Client;
use Google_Service_Gmail;
use Google_Service_Gmail_Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class GmailService
{
    protected $client;
    protected $service;
    protected $isAuthenticated = false;

    public function __construct()
    {
        $this->client = new Google_Client();
        $this->client->setApplicationName(config('app.name'));
        $this->client->setScopes(Google_Service_Gmail::GMAIL_SEND);
        $this->client->setAuthConfig(storage_path('app/credentials.json'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
        $this->client->setRedirectUri(url('http://localhost:8000/oauth2callback'));

        try {
            // Load or get new token
            $this->setAccessToken();
            $this->isAuthenticated = true;
            $this->service = new Google_Service_Gmail($this->client);
        } catch (\Exception $e) {
            Log::warning('GmailService not authenticated: ' . $e->getMessage());
            $this->isAuthenticated = false;
        }
    }

    private function setAccessToken()
    {
        $tokenPath = storage_path('app/gmail-token.json');
        
        if (!file_exists($tokenPath)) {
            throw new \Exception('Gmail token not found. Please run php artisan gmail:setup');
        }

        $accessToken = json_decode(file_get_contents($tokenPath), true);
        $this->client->setAccessToken($accessToken);

        // If token is expired, refresh it
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $accessToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                throw new \Exception('Refresh token is missing. Please re-authenticate.');
            }
            
            // Save the new token
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));
        }
    }

    public function isAuthenticated()
    {
        return $this->isAuthenticated;
    }

    // =========== NEW METHOD FOR ONLINE BOOKING CONFIRMATION ===========
    public function sendOnlineBookingConfirmation($reservation, $guest, $payment)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for online booking confirmation');
            return false;
        }

        try {
            $subject = "Booking Confirmed! Payment Received - AzureHotel";
            $message = $this->createOnlineBookingConfirmationTemplate($reservation, $guest, $payment);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send online booking confirmation email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendPaymentConfirmedEmail($reservation, $guest, $payment)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for payment confirmation');
            return false;
        }

        try {
            $subject = "Payment Confirmed - Booking Complete - AzureHotel";
            $message = $this->createPaymentConfirmedTemplate($reservation, $guest, $payment);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send payment confirmed email: ' . $e->getMessage());
            return false;
        }
    }
    // =========== ADD THIS METHOD FOR CREDIT CARD BOOKING CONFIRMATION ===========
    public function sendCreditCardBookingConfirmation($reservation, $guest, $payment)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for credit card booking confirmation');
            return false;
        }

        try {
            $subject = "Booking Confirmed  - AzureHotel";
            $message = $this->createCreditCardBookingConfirmationTemplate($reservation, $guest, $payment);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send credit card booking confirmation email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReservationCreatedEmail($reservation, $guest)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for reservation creation');
            return false;
        }

        try {
            $subject = "Reservation Created - Confirmation Required - AzureHotel";
            $message = $this->createReservationCreatedTemplate($reservation, $guest);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send reservation created email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReservationConfirmedEmail($reservation, $guest, $payment = null)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for reservation confirmation');
            return false;
        }

        try {
            $subject = "Reservation Confirmed - Booking Complete - AzureHotel";
            $message = $this->createReservationConfirmedTemplate($reservation, $guest, $payment);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send reservation confirmed email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReservationCancellationWarning($reservation, $guest)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for cancellation warning');
            return false;
        }

        try {
            $subject = "URGENT: Reservation Cancellation Warning - AzureHotel";
            $message = $this->createCancellationWarningTemplate($reservation, $guest);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation warning email: ' . $e->getMessage());
            return false;
        }
    }

    public function sendReservationCancelledEmail($reservation, $guest)
    {
        if (!$this->isAuthenticated) {
            Log::warning('GmailService not authenticated - email not sent for cancellation');
            return false;
        }

        try {
            $subject = "Reservation Cancelled - AzureHotel";
            $message = $this->createCancellationTemplate($reservation, $guest);
            return $this->sendEmail($guest->email, $subject, $message);
        } catch (\Exception $e) {
            Log::error('Failed to send cancellation email: ' . $e->getMessage());
            return false;
        }
    }

    private function sendEmail($to, $subject, $message)
    {
        try {
            // Prepare message
            $gmailMessage = new Google_Service_Gmail_Message();
            
            $rawMessage = "To: {$to}\r\n";
            $rawMessage .= "Subject: {$subject}\r\n";
            $rawMessage .= "MIME-Version: 1.0\r\n";
            $rawMessage .= "Content-Type: text/html; charset=utf-8\r\n";
            $rawMessage .= "\r\n" . $message;
            
            $encodedMessage = base64_encode($rawMessage);
            $encodedMessage = str_replace(['+', '/', '='], ['-', '_', ''], $encodedMessage);
            $gmailMessage->setRaw($encodedMessage);
            
            // Send message
            $this->service->users_messages->send('me', $gmailMessage);
            
            Log::info("Email sent successfully to: {$to} - Subject: {$subject}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send email to {$to}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get authorization URL for first-time setup
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Set authorization code for first-time setup
     */
    public function setAuthCode($code)
    {
        try {
            $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);
            $this->client->setAccessToken($accessToken);

            // Check if there was an error
            if (array_key_exists('error', $accessToken)) {
                throw new \Exception($accessToken['error_description'] ?? 'Authentication failed');
            }

            // Save the token to file
            $tokenPath = storage_path('app/gmail-token.json');
            if (!file_exists(dirname($tokenPath))) {
                mkdir(dirname($tokenPath), 0700, true);
            }
            file_put_contents($tokenPath, json_encode($this->client->getAccessToken()));

            return $accessToken;
        } catch (\Exception $e) {
            Log::error('Gmail authentication failed: ' . $e->getMessage());
            throw $e;
        }
    }

    // =========== NEW TEMPLATE FOR ONLINE BOOKING CONFIRMATION ===========
    private function createOnlineBookingConfirmationTemplate($reservation, $guest, $payment)
    {
        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        // Get room numbers
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = array_unique($roomNumbers);
        $roomNumbersStr = implode(', ', $roomNumbers);
        $numRooms = count($roomNumbers);

        // Calculate breakdown
        $roomPrice = $reservation->roomType->base_price;
        $subtotal = $roomPrice * $nights * $numRooms;
        
        // Format payment details
        $paymentMethod = 'Online Payment (Credit/Debit Card)';
        $paymentDate = $payment->payment_date 
            ? Carbon::parse($payment->payment_date)->format('F j, Y h:i A') 
            : now()->format('F j, Y h:i A');
        
        // Check if payment is from Stripe
        $isStripePayment = $payment->payment_method === 'online' && $payment->stripe_session_id;
        $transactionDisplay = $isStripePayment 
            ? "Stripe Transaction: " . ($payment->stripe_payment_intent_id ?? $payment->transaction_id)
            : "Transaction ID: " . $payment->transaction_id;

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
                    .header { background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%); color: white; padding: 30px 20px; text-align: center; }
                    .content { padding: 30px; }
                    .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e9ecef; }
                    .badge { background: #27ae60; color: white; padding: 10px 20px; border-radius: 30px; display: inline-block; font-weight: bold; margin-bottom: 20px; }
                    .section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2c5aa0; }
                    .payment-section { background: #e8f5e8; border-left: 4px solid #27ae60; }
                    .info-section { background: #e3f2fd; border-left: 4px solid #2196f3; }
                    .summary { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
                    .summary-item { background: white; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef; }
                    h3 { color: #2c5aa0; margin-top: 0; }
                    .highlight { color: #27ae60; font-weight: bold; }
                    .qr-code { text-align: center; margin: 20px 0; }
                    @media (max-width: 480px) {
                        .summary { grid-template-columns: 1fr; }
                        .header { padding: 20px 15px; }
                        .content { padding: 20px; }
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1 style='margin: 0; font-size: 28px;'>AzureHotel</h1>
                        <p style='margin: 10px 0 0; opacity: 0.9;'>Booking Confirmation</p>
                    </div>
                    
                    <div class='content'>
                        <div style='text-align: center;'>
                            <div class='badge'>✅ BOOKING CONFIRMED & PAID</div>
                        </div>
                        
                        <p>Dear <strong>{$guest->first_name} {$guest->last_name}</strong>,</p>
                        
                        <p>Thank you for choosing AzureHotel! Your online booking has been successfully processed and confirmed. 
                           Your payment has been received and your reservation is now guaranteed.</p>
                        
                        <div class='section payment-section'>
                            <h3>💰 Payment Information</h3>
                            <div class='summary'>
                                <div class='summary-item'>
                                    <strong>Payment Method:</strong><br>
                                    <span class='highlight'>{$paymentMethod}</span>
                                </div>
                                <div class='summary-item'>
                                    <strong>Amount Paid:</strong><br>
                                    <span class='highlight'>₱" . number_format($payment->amount, 2) . "</span>
                                </div>
                                <div class='summary-item'>
                                    <strong>Transaction:</strong><br>
                                    {$transactionDisplay}
                                </div>
                                <div class='summary-item'>
                                    <strong>Payment Date:</strong><br>
                                    {$paymentDate}
                                </div>
                            </div>
                            <p style='margin-top: 15px; padding: 10px; background: white; border-radius: 6px;'>
                                <strong>Payment Status:</strong> 
                                <span style='color: #27ae60; font-weight: bold;'>✅ Completed</span>
                            </p>
                        </div>
                        
                        <div class='section'>
                            <h3>📅 Booking Summary</h3>
                            <div class='summary'>
                                <div class='summary-item'>
                                    <strong>Booking Reference:</strong><br>
                                    {$payment->transaction_id}
                                </div>
                                <div class='summary-item'>
                                    <strong>Reservation ID:</strong><br>
                                    {$reservation->reservation_id}
                                </div>
                                <div class='summary-item'>
                                    <strong>Check-in Date:</strong><br>
                                    <span class='highlight'>{$checkIn->format('F j, Y')}</span><br>
                                    <small>From 2:00 PM</small>
                                </div>
                                <div class='summary-item'>
                                    <strong>Check-out Date:</strong><br>
                                    <span class='highlight'>{$checkOut->format('F j, Y')}</span><br>
                                    <small>Until 12:00 PM</small>
                                </div>
                                <div class='summary-item'>
                                    <strong>Duration:</strong><br>
                                    {$nights} night" . ($nights > 1 ? 's' : '') . "
                                </div>
                                <div class='summary-item'>
                                    <strong>Number of Guests:</strong><br>
                                    {$reservation->num_guests} guest" . ($reservation->num_guests > 1 ? 's' : '') . "
                                </div>
                                <div class='summary-item'>
                                    <strong>Room Type:</strong><br>
                                    {$reservation->roomType->type_name}
                                </div>
                                <div class='summary-item'>
                                    <strong>Room Number(s):</strong><br>
                                    {$roomNumbersStr}<br>
                                    <small>{$numRooms} room" . ($numRooms > 1 ? 's' : '') . "</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class='section'>
                            <h3>💵 Amount Breakdown</h3>
                            <div style='background: white; padding: 15px; border-radius: 6px;'>
                                <table style='width: 100%; border-collapse: collapse;'>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Room Rate (per night):</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($roomPrice, 2) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Subtotal ({$nights} nights × {$numRooms} rooms):</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($subtotal, 2) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Taxes & Fees:</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱0.00</td>
                                    </tr>
                                    <tr style='font-weight: bold;'>
                                        <td style='padding: 12px 0;'>Total Amount Paid:</td>
                                        <td style='padding: 12px 0; text-align: right; color: #27ae60;'>₱" . number_format($reservation->total_amount, 2) . "</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class='section info-section'>
                            <h3>📋 Important Information</h3>
                            <ul style='margin: 0; padding-left: 20px;'>
                                <li>Please bring a <strong>valid government-issued ID</strong> upon check-in</li>
                                <li>Check-in time: <strong>2:00 PM</strong> onwards</li>
                                <li>Check-out time: <strong>before 12:00 PM</strong></li>
                                <li>Early check-in and late check-out are subject to room availability</li>
                                <li>Special requests: <strong>" . ($reservation->special_requests ?: 'None') . "</strong></li>
                                <li>Cancellations must be made at least 24 hours before check-in</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;'>
                            <p style='margin: 0;'><strong>Need to modify or cancel your booking?</strong></p>
                            <p style='margin: 10px 0 0;'>Contact our reservations team immediately with your booking reference.</p>
                        </div>
                        
                        <p>We look forward to welcoming you to AzureHotel and ensuring you have a comfortable and memorable stay!</p>
                        
                        <p>Best regards,<br>
                        <strong>The AzureHotel Team</strong></p>
                    </div>
                    
                    <div class='footer'>
                        <p><strong>AzureHotel</strong><br>
                        📞 Contact: 09225548058 | 📧 Email: Inq_AzureHotel@gmail.com<br>
                        ⏰ Front Desk Hours: 24/7</p>
                        <p><em>Thank you for choosing AzureHotel for your accommodation needs!</em></p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    // =========== EXISTING TEMPLATE METHODS ===========

    private function createReservationCreatedTemplate($reservation, $guest)
    {
        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        // Get room numbers
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = implode(', ', array_unique($roomNumbers));

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2c5aa0; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                    .reservation-details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>AzureHotel</h1>
                        <h2>Reservation Created</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <p>Your reservation has been successfully created. Please complete your payment within 24 hours to confirm your booking.</p>
                        
                        <div class='reservation-details'>
                            <h3>Reservation Details:</h3>
                            <p><strong>Reservation ID:</strong> {$reservation->reservation_id}</p>
                            <p><strong>Check-in:</strong> {$checkIn->format('F j, Y')}</p>
                            <p><strong>Check-out:</strong> {$checkOut->format('F j, Y')}</p>
                            <p><strong>Nights:</strong> {$nights}</p>
                            <p><strong>Rooms:</strong> {$roomNumbers}</p>
                            <p><strong>Total Amount:</strong> ₱" . number_format($reservation->total_amount, 2) . "</p>
                            <p><strong>Guests:</strong> {$reservation->num_guests}</p>
                        </div>
                        
                        <p><strong>Important:</strong> This reservation will expire on " . $reservation->expires_at->format('F j, Y g:i A') . "</p>
                        
                        <p>Thank you for choosing AzureHotel!</p>
                    </div>
                    
                    <div class='footer'>
                        <p>AzureHotel<br>
                        Contact: 09225548058 | Email: Inq_AzureHotel@gmail.com</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    private function createReservationConfirmedTemplate($reservation, $guest, $payment = null)
    {
        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        // Get room numbers
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = implode(', ', array_unique($roomNumbers));

        $paymentSection = '';
        if ($payment) {
            $paymentSection = "
                <div class='payment-details'>
                    <h3>Payment Details:</h3>
                    <p><strong>Payment Method:</strong> " . ucfirst($payment->payment_method) . "</p>
                    <p><strong>Amount Paid:</strong> ₱" . number_format($payment->amount, 2) . "</p>
                    <p><strong>Payment Date:</strong> " . $payment->payment_date->format('F j, Y') . "</p>
                </div>
            ";
        }

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #27ae60; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                    .reservation-details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                    .payment-details { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>AzureHotel</h1>
                        <h2>Reservation Confirmed! 🎉</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <p>Your reservation has been confirmed! We look forward to welcoming you to AzureHotel.</p>
                        
                        <div class='reservation-details'>
                            <h3>Reservation Details:</h3>
                            <p><strong>Reservation ID:</strong> {$reservation->reservation_id}</p>
                            <p><strong>Check-in:</strong> {$checkIn->format('F j, Y')}</p>
                            <p><strong>Check-out:</strong> {$checkOut->format('F j, Y')}</p>
                            <p><strong>Nights:</strong> {$nights}</p>
                            <p><strong>Rooms:</strong> {$roomNumbers}</p>
                            <p><strong>Total Amount:</strong> ₱" . number_format($reservation->total_amount, 2) . "</p>
                            <p><strong>Guests:</strong> {$reservation->num_guests}</p>
                        </div>
                        
                        {$paymentSection}
                        
                        <p><strong>Check-in Instructions:</strong><br>
                        - Please bring a valid ID<br>
                        - Check-in time: 2:00 PM<br>
                        - Check-out time: 12:00 PM</p>
                        
                        <p>Thank you for choosing AzureHotel!</p>
                    </div>
                    
                    <div class='footer'>
                        <p>AzureHotel<br>
                        Contact: 09225548058 | Email: Inq_AzureHotel@gmail.com</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    private function createCancellationWarningTemplate($reservation, $guest)
    {
        $expiryTime = $reservation->expires_at->format('F j, Y g:i A');
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #e67e22; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                    .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>AzureHotel</h1>
                        <h2>Reservation Expiring Soon ⚠️</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <div class='warning'>
                            <h3>URGENT: Your reservation will expire in 1 hour!</h3>
                            <p>Your reservation #{$reservation->reservation_id} is about to expire at {$expiryTime}.</p>
                        </div>
                        
                        <p>To keep your reservation active, please complete your payment immediately.</p>
                        
                        <p><strong>Total Amount Due:</strong> ₱" . number_format($reservation->total_amount, 2) . "</p>
                        
                        <p>If we don't receive payment within the next hour, your reservation will be automatically cancelled.</p>
                        
                        <p>Thank you,<br>AzureHotel Team</p>
                    </div>
                    
                    <div class='footer'>
                        <p>AzureHotel<br>
                        Contact: 09225548058 | Email: Inq_AzureHotel@gmail.com</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    private function createCancellationTemplate($reservation, $guest)
    {
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #e74c3c; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Azure Hotel</h1>
                        <h2>Reservation Cancelled</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <p>We're sorry to inform you that your reservation #{$reservation->reservation_id} has been cancelled.</p>
                        
                        <p><strong>Cancellation Reason:</strong> {$reservation->cancellation_reason}</p>
                        
                        <p>If this was a mistake or you have any questions, please contact us immediately.</p>
                        
                        <p>We hope to serve you in the future.</p>
                        
                        <p>Sincerely,<br>AzureHotel Team</p>
                    </div>

                    <div class='footer'>
                        <p>AzureHotel<br>
                        Contact: 09225548058 | Email: Inq_AzureHotel@gmail.com</p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    private function createPaymentConfirmedTemplate($reservation, $guest, $payment)
    {
        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        // Get room numbers
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = implode(', ', array_unique($roomNumbers));

        // Format payment method display
        $paymentMethod = ucfirst(str_replace('_', ' ', $payment->payment_method));
        $paymentDate = $payment->payment_date ? Carbon::parse($payment->payment_date)->format('F j, Y g:i A') : now()->format('F j, Y g:i A');
        
        // Format transaction ID based on payment method
        $transactionId = $payment->transaction_id;
        if ($payment->payment_method === 'cash') {
            $transactionId = "Cash Payment - " . ($payment->transaction_id ?? 'N/A');
        } elseif ($payment->payment_method === 'credit_card') {
            $transactionId = "Card Payment - " . ($payment->transaction_id ?? 'N/A');
        } elseif ($payment->payment_method === 'online') {
            $transactionId = "Online Payment - " . ($payment->transaction_id ?? 'N/A');
        }

        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #27ae60; color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 20px; }
                    .footer { background: #eee; padding: 10px; text-align: center; font-size: 12px; }
                    .reservation-details { background: white; padding: 15px; border-radius: 5px; margin: 15px 0; }
                    .payment-details { background: #e8f5e8; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #27ae60; }
                    .success-badge { background: #27ae60; color: white; padding: 10px 15px; border-radius: 5px; display: inline-block; margin: 10px 0; }
                    .instructions { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #2196f3; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>AzureHotel</h1>
                        <h2>Payment Confirmed! 🎉</h2>
                    </div>
                    
                    <div class='content'>
                        <div class='success-badge'>
                            <strong>✅ Payment Successful! Your reservation is now confirmed.</strong>
                        </div>
                        
                        <p>Dear {$guest->first_name} {$guest->last_name},</p>
                        
                        <p>We're pleased to inform you that your payment has been processed successfully and your reservation is now confirmed.</p>
                        
                        <div class='payment-details'>
                            <h3>💰 Payment Details:</h3>
                            <p><strong>Payment Method:</strong> {$paymentMethod}</p>
                            <p><strong>Amount Paid:</strong> ₱" . number_format($payment->amount, 2) . "</p>
                            <p><strong>Transaction ID:</strong> {$transactionId}</p>
                            <p><strong>Payment Date:</strong> {$paymentDate}</p>
                            <p><strong>Payment Status:</strong> <span style='color: #27ae60; font-weight: bold;'>Completed</span></p>
                        </div>
                        
                        <div class='reservation-details'>
                            <h3>📅 Reservation Details:</h3>
                            <p><strong>Reservation ID:</strong> {$reservation->reservation_id}</p>
                            <p><strong>Check-in:</strong> {$checkIn->format('F j, Y')} (2:00 PM)</p>
                            <p><strong>Check-out:</strong> {$checkOut->format('F j, Y')} (12:00 PM)</p>
                            <p><strong>Duration:</strong> {$nights} night" . ($nights > 1 ? 's' : '') . "</p>
                            <p><strong>Room(s):</strong> {$roomNumbers}</p>
                            <p><strong>Total Amount:</strong> ₱" . number_format($reservation->total_amount, 2) . "</p>
                            <p><strong>Number of Guests:</strong> {$reservation->num_guests}</p>
                        </div>
                        
                        <div class='instructions'>
                            <h3>📋 Check-in Instructions:</h3>
                            <p>• Please bring a valid government-issued ID</p>
                            <p>• Check-in time: 2:00 PM</p>
                            <p>• Check-out time: 12:00 PM</p>
                            <p>• Early check-in/late check-out subject to availability</p>
                            <p>• Contact us for any special requests or assistance</p>
                        </div>
                        
                        <p>We look forward to welcoming you to AzureHotel and ensuring you have a comfortable and memorable stay!</p>
                        
                        <p>Warm regards,<br>
                        <strong>The AzureHotel Team</strong></p>
                    </div>
                    
                    <div class='footer'>
                        <p><strong>AzureHotel</strong><br>
                        📞 Contact: 09225548058 | 📧 Email: Inq_AzureHotel@gmail.com<br>
                        ⏰ Front Desk Hours: 24/7</p>
                        <p><em>Thank you for choosing AzureHotel for your accommodation needs!</em></p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

    private function createCreditCardBookingConfirmationTemplate($reservation, $guest, $payment)
    {
        $checkIn = Carbon::parse($reservation->check_in_date);
        $checkOut = Carbon::parse($reservation->check_out_date);
        $nights = $checkIn->diffInDays($checkOut);
        
        // Get room numbers
        $roomNumbers = [];
        foreach($reservation->bookings as $booking) {
            foreach($booking->rooms as $bookingRoom) {
                $roomNumbers[] = $bookingRoom->room->room_number;
            }
        }
        $roomNumbers = array_unique($roomNumbers);
        $roomNumbersStr = implode(', ', $roomNumbers);
        $numRooms = count($roomNumbers);

        // Calculate breakdown
        $roomPrice = $reservation->roomType->base_price;
        $subtotal = $roomPrice * $nights * $numRooms;
        
        return "
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
                    .header { background: linear-gradient(135deg, #2c5aa0 0%, #1e3a8a 100%); color: white; padding: 30px 20px; text-align: center; }
                    .content { padding: 30px; }
                    .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #e9ecef; }
                    .badge { background: #27ae60; color: white; padding: 10px 20px; border-radius: 30px; display: inline-block; font-weight: bold; margin-bottom: 20px; }
                    .section { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #2c5aa0; }
                    .payment-section { background: #fff8e1; border-left: 4px solid #ffc107; }
                    .info-section { background: #e3f2fd; border-left: 4px solid #2196f3; }
                    .warning-section { background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 6px; }
                    .summary { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px; }
                    .summary-item { background: white; padding: 15px; border-radius: 6px; border: 1px solid #e9ecef; }
                    h3 { color: #2c5aa0; margin-top: 0; }
                    .highlight { color: #27ae60; font-weight: bold; }
                    .warning { color: #d35400; font-weight: bold; }
                    @media (max-width: 480px) {
                        .summary { grid-template-columns: 1fr; }
                        .header { padding: 20px 15px; }
                        .content { padding: 20px; }
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1 style='margin: 0; font-size: 28px;'>AzureHotel</h1>
                        <p style='margin: 10px 0 0; opacity: 0.9;'>Booking Confirmation</p>
                    </div>
                    
                    <div class='content'>
                        <div style='text-align: center;'>
                            <div class='badge'>✅ BOOKING CONFIRMED</div>
                            <p style='color: #d35400; font-weight: bold;'>💳 Credit Card Payment Required at Check-in</p>
                        </div>
                        
                        <p>Dear <strong>{$guest->first_name} {$guest->last_name}</strong>,</p>
                        
                        <p>Thank you for choosing AzureHotel! Your booking has been successfully created and confirmed. 
                           Please note that payment will be processed at the hotel using your credit card upon check-in.</p>
                        
                        <div class='section payment-section'>
                            <h3>💳 Payment Information</h3>
                            <div class='summary'>
                                <div class='summary-item'>
                                    <strong>Payment Method:</strong><br>
                                    <span class='warning'>Credit Card (Pay at Hotel)</span>
                                </div>
                                <div class='summary-item'>
                                    <strong>Amount Due:</strong><br>
                                    <span class='highlight'>₱" . number_format($payment->amount, 2) . "</span>
                                </div>
                                <div class='summary-item'>
                                    <strong>Booking Reference:</strong><br>
                                    {$payment->transaction_id}
                                </div>
                                <div class='summary-item'>
                                    <strong>Reservation ID:</strong><br>
                                    {$reservation->reservation_id}
                                </div>
                            </div>
                            <div class='warning-section'>
                                <p style='margin: 0;'><strong>⚠️ Important Payment Notice:</strong></p>
                                <p style='margin: 10px 0 0;'>Please bring the same credit card used for booking and a valid government-issued ID to the hotel for payment processing.</p>
                            </div>
                        </div>
                        
                        <div class='section'>
                            <h3>📅 Booking Summary</h3>
                            <div class='summary'>
                                <div class='summary-item'>
                                    <strong>Check-in Date:</strong><br>
                                    <span class='highlight'>{$checkIn->format('F j, Y')}</span><br>
                                    <small>From 2:00 PM</small>
                                </div>
                                <div class='summary-item'>
                                    <strong>Check-out Date:</strong><br>
                                    <span class='highlight'>{$checkOut->format('F j, Y')}</span><br>
                                    <small>Until 12:00 PM</small>
                                </div>
                                <div class='summary-item'>
                                    <strong>Duration:</strong><br>
                                    {$nights} night" . ($nights > 1 ? 's' : '') . "
                                </div>
                                <div class='summary-item'>
                                    <strong>Number of Guests:</strong><br>
                                    {$reservation->num_guests} guest" . ($reservation->num_guests > 1 ? 's' : '') . "
                                </div>
                                <div class='summary-item'>
                                    <strong>Room Type:</strong><br>
                                    {$reservation->roomType->type_name}
                                </div>
                                <div class='summary-item'>
                                    <strong>Room Number(s):</strong><br>
                                    {$roomNumbersStr}<br>
                                    <small>{$numRooms} room" . ($numRooms > 1 ? 's' : '') . "</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class='section'>
                            <h3>💵 Amount Breakdown</h3>
                            <div style='background: white; padding: 15px; border-radius: 6px;'>
                                <table style='width: 100%; border-collapse: collapse;'>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Room Rate (per night):</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($roomPrice, 2) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Subtotal ({$nights} nights × {$numRooms} rooms):</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱" . number_format($subtotal, 2) . "</td>
                                    </tr>
                                    <tr>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee;'>Taxes & Fees:</td>
                                        <td style='padding: 8px 0; border-bottom: 1px solid #eee; text-align: right;'>₱0.00</td>
                                    </tr>
                                    <tr style='font-weight: bold;'>
                                        <td style='padding: 12px 0;'>Total Amount Due:</td>
                                        <td style='padding: 12px 0; text-align: right; color: #27ae60;'>₱" . number_format($reservation->total_amount, 2) . "</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class='section info-section'>
                            <h3>📋 Important Information</h3>
                            <ul style='margin: 0; padding-left: 20px;'>
                                <li>Please bring a <strong>valid government-issued ID</strong> and the <strong>same credit card</strong> used for booking</li>
                                <li>Check-in time: <strong>2:00 PM</strong> onwards</li>
                                <li>Check-out time: <strong>before 12:00 PM</strong></li>
                                <li>Early check-in and late check-out are subject to room availability</li>
                                <li>Special requests: <strong>" . ($reservation->special_requests ?: 'None') . "</strong></li>
                                <li>Cancellations must be made at least 24 hours before check-in</li>
                                <li>Your reservation will be held until 6:00 PM on the check-in date</li>
                            </ul>
                        </div>
                        
                        <div style='text-align: center; margin: 30px 0; padding: 20px; background: #f8f9fa; border-radius: 8px;'>
                            <p style='margin: 0;'><strong>Need to modify or cancel your booking?</strong></p>
                            <p style='margin: 10px 0 0;'>Contact our reservations team immediately with your booking reference.</p>
                        </div>
                        
                        <p>We look forward to welcoming you to AzureHotel and ensuring you have a comfortable and memorable stay!</p>
                        
                        <p>Best regards,<br>
                        <strong>The AzureHotel Team</strong></p>
                    </div>
                    
                    <div class='footer'>
                        <p><strong>AzureHotel</strong><br>
                        📞 Contact: 09225548058 | 📧 Email: Inq_AzureHotel@gmail.com<br>
                        ⏰ Front Desk Hours: 24/7</p>
                        <p><em>Thank you for choosing AzureHotel for your accommodation needs!</em></p>
                    </div>
                </div>
            </body>
            </html>
        ";
    }

}